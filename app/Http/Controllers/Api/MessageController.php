<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Conversation;
use App\Models\FamilyMember;
use App\Models\Message;
use App\Models\User;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MessageController extends Controller
{
    public function messages(Request $request)
    {
        $user = Auth::user();

        $familyIds = FamilyMember::where('user_id', $user->id)->pluck('family_id');
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

                return [$otherUserId => $this->formatConversation($conversation, $user)];
            });

        $userIds = FamilyMember::whereIn('family_id', $familyIds)
            ->where('user_id', '!=', $user->id)
            ->pluck('user_id')
            ->unique();

        $contacts = User::query()
            ->whereIn('id', $userIds)
            ->whereKeyNot($user->id)
            ->orderBy('name')
            ->get(['id', 'name', 'image'])
            ->map(function (User $contact) use ($conversationByUserId) {
                $conversation = $conversationByUserId->get((int) $contact->id);

                return [
                    'id' => $contact->id,
                    'name' => $contact->name,
                    'image' => $contact->image,
                    'conversation_id' => $conversation['id'] ?? null,
                    'preview_text' => $conversation['preview_text'] ?? 'No messages yet',
                    'last_message_at' => $conversation['last_message_at'] ?? null,
                    'unread_count' => $conversation['unread_count'] ?? 0,
                ];
            })
            ->values();

        $selectedUserId = (int) $request->query('user');
        $activeContact = $selectedUserId
            ? $contacts->firstWhere('id', $selectedUserId)
            : $contacts->first();

        $activeConversation = null;
        $activeMessages = [];
        $activeReadCount = 0;
        $activeUnreadCount = 0;

        if ($activeContact) {
            $conversation = $conversations->first(function (Conversation $conversation) use ($activeContact, $user) {
                $otherUserId = (int) $conversation->participants
                    ->where('user_id', '!=', $user->id)
                    ->pluck('user_id')
                    ->first();

                return $otherUserId === (int) $activeContact['id'];
            });

            if ($conversation) {
                $participant = $conversation->participants
                    ->firstWhere('user_id', $user->id);

                if ($participant) {
                    $participant->forceFill(['last_read_at' => now()])->save();
                }

                $activeConversation = $this->formatConversation($conversation, $user);
                $activeMessages = $activeConversation['messages'];
                $activeReadCount = collect($activeMessages)
                    ->where('is_mine', false)
                    ->count();
                $activeUnreadCount = 0;
            }
        }

        return ApiResponse::success('Messages retrieved successfully.', [
            'contacts' => $contacts,
            'active_contact' => $activeContact,
            'active_conversation' => $activeConversation,
            'active_messages' => $activeMessages,
            'active_read_count' => $activeReadCount,
            'active_unread_count' => $activeUnreadCount,
        ]);
    }

    public function pollMessages(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'since_id' => ['nullable', 'integer', 'min:0'],
        ]);

        $user = Auth::user();
        $familyIds = FamilyMember::where('user_id', $user->id)->pluck('family_id');
        $userIds = FamilyMember::whereIn('family_id', $familyIds)
            ->where('user_id', '!=', $user->id)
            ->pluck('user_id')
            ->unique();

        $targetUser = User::where('id', $data['user_id'])
            ->whereIn('id', $userIds)
            ->firstOrFail();

        $conversation = $this->findDirectConversation($user->id, $targetUser->id);

        if (! $conversation) {
            return ApiResponse::success('Messages polled successfully.', [
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
            ->with('sender:id,name,image', 'attachments')
            ->where('id', '>', $sinceId)
            ->orderBy('id')
            ->get()
            ->map(fn (Message $message) => $this->formatMessage($message, $user))
            ->values();

        $incomingCount = $conversation->messages()
            ->where('sender_user_id', '!=', $user->id)
            ->count();

        return ApiResponse::success('Messages polled successfully.', [
            'messages' => $messages,
            'latest_id' => $messages->last()['id'] ?? $sinceId,
            'conversation_id' => $conversation->id,
            'read_count' => $incomingCount,
            'unread_count' => 0,
        ]);
    }

    public function storeMessage(Request $request)
    {
        $data = $request->validate([
            'conversation_id' => ['nullable', 'integer', 'exists:conversations,id'],
            'recipient_user_id' => ['required_without:conversation_id', 'integer', 'exists:users,id'],
            'message' => ['nullable', 'string', 'max:5000'],
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
            return ApiResponse::error('You cannot message yourself.', null, 422);
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

        $messageText = trim((string) ($data['message'] ?? ''));

        $hasAttachment = $request->hasFile('attachment');

        if ($messageText === '' && ! $hasAttachment) {
            return ApiResponse::error('Write a message or attach a file.', ['message' => ['Write a message or attach a file.']], 422);
        }

        $messageType = $data['message_type'] ?? 'text';

        $message = $conversation->messages()->create([
            'sender_user_id' => $user->id,
            'message_type' => $messageType,
            'body' => $messageText !== '' ? $messageText : null,
        ])->load('sender:id,name,image');

        if ($request->hasFile('attachment')) {
            foreach ($request->file('attachment') as $file) {
                $path = $file->store('messages', 'public');
                $type = $this->inferAttachmentMessageType($file);

                $message->attachments()->create([
                    'message_id' => $message->id,
                    'path' => $path,
                    'type' => $type,
                ]);
            }
        }

        $conversation->forceFill([
            'last_message_at' => now(),
        ])->save();

        $conversation->participants()
            ->where('user_id', $user->id)
            ->update(['last_read_at' => now()]);

        return ApiResponse::success('Message sent successfully.', [
            'message' => $this->formatMessage($message, $user),
            'conversation_id' => $conversation->id,
        ]);
    }

    private function findDirectConversation(int $currentUserId, int $targetUserId): ?Conversation
    {
        return Conversation::query()
            ->where('conversation_type', 'direct')
            ->whereHas('participants', function ($query) use ($currentUserId) {
                $query->where('user_id', $currentUserId)
                    ->whereNull('left_at');
            })
            ->whereHas('participants', function ($query) use ($targetUserId) {
                $query->where('user_id', $targetUserId)
                    ->whereNull('left_at');
            })
            ->with([
                'participants.user:id,name,image',
                'messages' => function ($query) {
                    $query->with('sender:id,name,image', 'attachments')->orderBy('created_at', 'asc');
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

    private function formatConversation(Conversation $conversation, User $user): array
    {
        $messages = $conversation->messages
            ->map(fn (Message $message) => $this->formatMessage($message, $user))
            ->values()
            ->all();

        return [
            'id' => $conversation->id,
            'conversation_type' => $conversation->conversation_type,
            'display_name' => $conversation->displayTitleFor($user),
            'initials' => $conversation->initialsFor($user),
            'preview_text' => $conversation->latestMessage
                ? $this->conversationPreview($conversation->latestMessage)
                : 'No messages yet',
            'unread_count' => $conversation->unreadCountFor($user),
            'last_message_at' => $conversation->last_message_at,
            'messages' => $messages,
        ];
    }

    private function formatMessage(Message $message, User $user): array
    {
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
    }

    private function conversationPreview(Message $message): string
    {
        if ($message->message_type === 'text') {
            return Str::limit((string) $message->body, 40);
        }

        return ucfirst(str_replace('_', ' ', (string) $message->message_type));
    }

    private function inferAttachmentMessageType(?\Illuminate\Http\UploadedFile $file): string
    {
        if (! $file) {
            return 'file';
        }

        $mime = $file->getMimeType() ?: '';

        return match (true) {
            str_starts_with($mime, 'image/') => 'image',
            str_starts_with($mime, 'audio/') => 'audio',
            str_starts_with($mime, 'video/') => 'video',
            default => 'file',
        };
    }
}
