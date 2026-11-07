<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\FamilyMember;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MessageController extends Controller
{
    public function messages(Request $request)
    {
        $user = Auth::user();
        $conversations = $user->conversations()
            ->where('conversation_type', 'direct')
            ->with([
                'participants.user:id,name,image',
                'messages' => function ($query) {
                    $query->with('sender:id,name,image', 'attachments')->orderBy('created_at', 'asc');
                },
                'latestMessage.sender:id,name,image',
            ])
            ->orderByDesc('last_message_at')
            ->orderByDesc('created_at')
            ->get();

        $conversationByUserId = $conversations
            ->mapWithKeys(function (Conversation $conversation) use ($user) {
                $otherUserId = (int) $conversation->participants
                    ->where('user_id', '!=', $user->id)
                    ->pluck('user_id')
                    ->first();

                if (! $otherUserId) {
                    return [];
                }

                return [$otherUserId => $conversation];
            });

        // Current user's family IDs
        $familyIds = FamilyMember::where('user_id', $user->id)->pluck('family_id');
        // All user IDs in those families
        $userIds = FamilyMember::whereIn('family_id', $familyIds)
            ->where('user_id', '!=', $user->id)
            ->pluck('user_id')
            ->unique();

        $contacts = User::query()
            ->whereIn('id', $userIds)
            ->whereKeyNot($user->id)
            ->orderBy('name')
            ->get(['id', 'name', 'image'])
            ->map(function (User $contact) use ($conversationByUserId, $user) {
                /** @var Conversation|null $conversation */
                $conversation = $conversationByUserId->get((int) $contact->id);

                if (! $conversation) {
                    $contact->conversation_id = null;
                    $contact->preview_text = 'No messages yet';
                    $contact->last_message_at = null;
                    $contact->unread_count = 0;

                    return $contact;
                }

                $conversation->display_name = $conversation->displayTitleFor($user);
                $conversation->initials = $conversation->initialsFor($user);
                $conversation->preview_text = $conversation->latestMessage
                    ? $this->conversationPreview($conversation->latestMessage)
                    : 'No messages yet';
                $conversation->unread_count = $conversation->unreadCountFor($user);
                $contact->conversation_id = $conversation->id;
                $contact->preview_text = $conversation->preview_text;
                $contact->last_message_at = $conversation->latestMessage?->created_at;
                $contact->unread_count = $conversation->unread_count;

                return $contact;
            });

        $selectedUserId = (int) $request->query('user');
        $activeContact = $selectedUserId
            ? $contacts->firstWhere('id', $selectedUserId)
            : $contacts->first();

        $activeMessages = collect();
        $activeReadCount = 0;
        $activeUnreadCount = 0;
        $activeConversation = null;

        if ($activeContact) {
            $activeConversation = $conversations->first(function (Conversation $conversation) use ($activeContact, $user) {
                $otherUserId = (int) $conversation->participants
                    ->where('user_id', '!=', $user->id)
                    ->pluck('user_id')
                    ->first();

                return $otherUserId === (int) $activeContact->id;
            });
        }

        if ($activeConversation instanceof Conversation) {
            $activeMessages = $activeConversation->messages;

            $participant = $activeConversation->participants
                ->firstWhere('user_id', $user->id);

            if ($participant) {
                $participant->forceFill(['last_read_at' => now()])->save();
            }

            $activeConversation->unread_count = 0;
            $activeConversation->read_count = $activeMessages
                ->where('sender_user_id', '!=', $user->id)
                ->count();

            $activeReadCount = $activeConversation->read_count;
            $activeUnreadCount = $activeConversation->unread_count;

            // Hide unread badge for the currently opened user immediately.
            $contacts = $contacts->map(function (User $contact) use ($activeContact) {
                if ($activeContact && (int) $contact->id === (int) $activeContact->id) {
                    $contact->unread_count = 0;
                }

                return $contact;
            });
        }

        return view('user.messages', compact(
            'contacts',
            'activeContact',
            'activeConversation',
            'activeMessages',
            'activeReadCount',
            'activeUnreadCount'
        ));
    }

    public function pollMessages(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'since_id' => ['nullable', 'integer', 'min:0'],
        ]);

        $user = Auth::user();
        // Current user's family IDs
        $familyIds = FamilyMember::where('user_id', $user->id)->pluck('family_id');
        // All user IDs in those families
        $userIds = FamilyMember::whereIn('family_id', $familyIds)
            ->where('user_id', '!=', $user->id)
            ->pluck('user_id')
            ->unique();
        $targetUser = User::where('id', $data['user_id'])
            ->whereIn('id', $userIds)
            ->firstOrFail();
        $conversation = $this->findDirectConversation($user->id, $targetUser->id);

        if (! $conversation) {
            return response()->json([
                'messages' => [],
                'latest_id' => (int) ($data['since_id'] ?? 0),
                'conversation_id' => null,
                'read_count' => 0,
                'unread_count' => 0,
            ]);
        }

        $conversation->participants()
            ->where('user_id', $user->id)
            ->update(['last_read_at' => now()]);

        $sinceId = (int) ($data['since_id'] ?? 0);

        $messages = $conversation->messages()
            ->with('sender:id,name', 'attachments')
            ->where('id', '>', $sinceId)
            ->orderBy('id')
            ->get()
            ->map(function (Message $message) use ($user) {
                $senderName = $message->sender->name ?? 'User';

                $senderInitials = collect(preg_split('/\s+/', trim($senderName)) ?: [])
                    ->filter()
                    ->take(2)
                    ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
                    ->implode('');

                $attachments = $message->attachments->map(fn ($attachment) => [
                    'url' => asset('storage/'.$attachment->path),
                    'type' => $attachment->type,
                ]);

                return [
                    'id' => $message->id,
                    'is_mine' => (int) $message->sender_user_id === (int) $user->id,
                    'sender_name' => $senderName,
                    'sender_initials' => $senderInitials ?: 'ME',
                    'body' => $message->body,
                    'message_type' => $message->message_type,
                    'attachments' => $attachments,
                    'time' => $message->created_at?->format('h:i A') ?? '',
                ];
            })
            ->values();

        $incomingCount = $conversation->messages()
            ->where('sender_user_id', '!=', $user->id)
            ->count();

        return response()->json([
            'messages' => $messages,
            'latest_id' => $messages->last()['id'] ?? $sinceId,
            'conversation_id' => $conversation->id,
            'read_count' => $incomingCount,
            'unread_count' => 0,
        ]);
    }


    public function storeMessage(Request $request)
    {
        try {
            $data = $request->validate([
                'conversation_id' => ['nullable', 'integer'],
                'recipient_user_id' => ['required_without:conversation_id', 'integer'],
                'message' => ['nullable', 'string'],
                'attachment' => ['nullable', 'array'],
                'attachment.*' => ['file', 'mimes:jpg,jpeg,png,pdf,webm,ogg,mp3,wav', 'max:10240'],
                'message_type' => ['nullable', 'string', 'in:text,image,audio,video,file,voice'],
            ]);

            $user = Auth::user();

            $familyIds = FamilyMember::where('user_id', $user->id)->pluck('family_id');

            $userIds = FamilyMember::whereIn('family_id', $familyIds)
                ->where('user_id', '!=', $user->id)
                ->pluck('user_id')
                ->unique();

            $recipientUserId = (int) ($data['recipient_user_id'] ?? 0);

            if ($recipientUserId === $user->id) {
                return back()->with('error', 'You cannot message yourself.');
            }

            $conversation = null;
            if (! empty($data['conversation_id'])) {
                $conversation = Conversation::query()
                    ->whereKey($data['conversation_id'])
                    ->whereHas('participants', function ($query) use ($user) {
                        $query->where('user_id', $user->id)->whereNull('left_at');
                    })
                    ->firstOrFail();
            } else {
                $recipient = User::where('id', $recipientUserId)
                    ->whereIn('id', $userIds)
                    ->firstOrFail();
                $conversation = $this->findOrCreateDirectConversation($user, $recipient);
            }

            if (! $request->filled('message') && ! $request->hasFile('attachment')) {
                return back()->withErrors(['message' => 'Please write a message or select a file.']);
            }

            $message = $conversation->messages()->create([
                'sender_user_id' => $user->id,
                'body' => $request->message,
                'message_type' => $request->message_type ?? 'text',
            ]);

            if ($request->hasFile('attachment')) {
                foreach ($request->file('attachment') as $file) {
                    $path = $file->store('messages', 'public');
                    $mime = $file->getMimeType();

                    $fileType = 'file';
                    if (str_contains($mime, 'image/')) {
                        $fileType = 'image';
                    } elseif (str_contains($mime, 'audio/') || str_contains($mime, 'video/webm')) {
                        $fileType = 'audio';
                    }

                    $message->attachments()->create([
                        'message_id' => $message->id,
                        'path' => $path,
                        'type' => $fileType,
                    ]);
                }
            }

            $conversation->update(['last_message_at' => now()]);

            return $request->ajax()
                ? response()->json(['success' => true])
                : redirect()->back()->with('success', 'Message sent.');

        } catch (\Exception $e) {
            Log::error($e);
            return back()->with('error', 'Unable to send message right now.');
        }
    }

    // public function storeMessage(Request $request)
    // {
    //     $data = $request->validate([
    //         'conversation_id' => ['nullable', 'integer', 'exists:conversations,id'],
    //         'recipient_user_id' => ['required_without:conversation_id', 'integer', 'exists:users,id'],
    //         'message' => ['nullable', 'string', 'max:5000'],
    //     ]);

    //     $user = Auth::user();

    //     // Current user's family IDs
    //     $familyIds = FamilyMember::where('user_id', $user->id)->pluck('family_id');
    //     // All user IDs in those families
    //     $userIds = FamilyMember::whereIn('family_id', $familyIds)
    //         ->where('user_id', '!=', $user->id)
    //         ->pluck('user_id')
    //         ->unique();

    //     $recipientUserId = (int) ($data['recipient_user_id'] ?? 0);

    //     if ($recipientUserId === $user->id) {
    //         return back()->with('error', 'You cannot message yourself.');
    //     }

    //     $conversation = null;
    //     if (! empty($data['conversation_id'])) {
    //         $conversation = Conversation::query()
    //             ->whereKey($data['conversation_id'])
    //             ->whereHas('participants', function ($query) use ($user) {
    //                 $query->where('user_id', $user->id)->whereNull('left_at');
    //             })
    //             ->firstOrFail();
    //     } else {
    //         $recipient = User::where('id', $recipientUserId)
    //             ->whereIn('id', $userIds)
    //             ->firstOrFail();
    //         $conversation = $this->findOrCreateDirectConversation($user, $recipient);
    //     }

    //     $messageText = trim((string) ($data['message'] ?? ''));

    //     $messageType = 'text';

    //     if ($messageText === '') {
    //         return back()
    //             ->withErrors(['message' => 'Write a message or attach a file.'])
    //             ->withInput();
    //     }

    //     $conversation->messages()->create([
    //         'sender_user_id' => $user->id,
    //         'message_type' => $messageType,
    //         'body' => $messageText !== '' ? $messageText : null
    //     ]);

    //     $conversation->forceFill([
    //         'last_message_at' => now(),
    //     ])->save();

    //     $conversation->participants()
    //         ->where('user_id', $user->id)
    //         ->update(['last_read_at' => now()]);

    //     return redirect()
    //         ->route('user.messages', ['user' => $recipientUserId ?: $this->directRecipientId($conversation, $user->id)])
    //         ->with('success', 'Message sent successfully.');
    // }

    private function findDirectConversation(int $currentUserId, int $targetUserId): ?Conversation
    {
        return Conversation::query()
            ->where('conversation_type', 'direct')
            ->whereHas('participants', function ($query) use ($currentUserId) {
                $query->where('user_id', $currentUserId)
                    ->whereNull('left_at');
            })
            ->whereHas('participants.user', function ($q) use ($currentUserId) {
                $q->whereIn('id', function ($query) use ($currentUserId) {
                    $query->select('user_id')
                        ->from('family_members')
                        ->whereIn('family_id', function ($sub) use ($currentUserId) {
                            $sub->select('family_id')
                                ->from('family_members')
                                ->where('user_id', $currentUserId);
                        });
                });
            })
            ->whereHas('participants', function ($query) use ($targetUserId) {
                $query->where('user_id', $targetUserId)
                    ->whereNull('left_at');
            })
            ->with([
                'participants.user:id,name,image',
                'messages' => function ($query) {
                    $query->with('sender:id,name,image', 'attachments')
                        ->orderBy('created_at', 'asc');
                },
                'latestMessage.sender:id,name,image',
            ])
            ->first();
    }

    private function findOrCreateDirectConversation(User $currentUser, User $targetUser): Conversation
    {
        $conversation = $this->findDirectConversation($currentUser->id, $targetUser->id);

        if ($conversation) {
            return $conversation;
        }

        $conversation = Conversation::create([
            'conversation_type' => 'direct',
            'title' => null,
            'created_by_user_id' => $currentUser->id,
            'last_message_at' => null,
        ]);

        $conversation->participants()->createMany([
            [
                'user_id' => $currentUser->id,
                'role' => 'member',
            ],
            [
                'user_id' => $targetUser->id,
                'role' => 'member',
            ],
        ]);

        return $conversation->load([
            'participants.user:id,name,image',
            'messages' => function ($query) {
                $query->with('sender:id,name,image', 'attachments')->orderBy('created_at', 'asc');
            },
            'latestMessage.sender:id,name,image',
        ]);
    }

    private function directRecipientId(Conversation $conversation, int $currentUserId): int
    {
        return (int) $conversation->participants
            ->where('user_id', '!=', $currentUserId)
            ->pluck('user_id')
            ->first();
    }

    private function conversationPreview(Message $message): string
    {
        return match ($message->message_type) {
            'image' => 'Photo',
            'voice' => 'Voice note',
            default => Str::limit(trim((string) $message->body), 64) ?: 'No message text',
        };
    }
}
