<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_type',
        'title',
        'created_by_user_id',
        'last_message_at',
    ];

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(ConversationParticipant::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function displayTitleFor(User $user): string
    {
        if ($this->conversation_type === 'group' && filled($this->title)) {
            return $this->title;
        }

        $otherParticipant = $this->participants
            ->first(fn (ConversationParticipant $participant) => (int) $participant->user_id !== (int) $user->id);

        return $otherParticipant?->user?->name
            ?? $this->title
            ?? 'Conversation';
    }

    public function initialsFor(User $user): string
    {
        $name = $this->displayTitleFor($user);
        $parts = preg_split('/\s+/', trim($name)) ?: [];

        $initials = collect($parts)
            ->filter()
            ->take(2)
            ->map(fn (string $part) => strtoupper(substr($part, 0, 1)))
            ->implode('');

        return $initials ?: 'ME';
    }

    public function unreadCountFor(User $user): int
    {
        $participant = $this->participants
            ->first(fn (ConversationParticipant $participant) => (int) $participant->user_id === (int) $user->id);

        $lastReadAt = $participant?->last_read_at;

        return $this->messages
            ->filter(function (Message $message) use ($user, $lastReadAt) {
                if ((int) $message->sender_user_id === (int) $user->id) {
                    return false;
                }

                return !$lastReadAt || $message->created_at->gt($lastReadAt);
            })
            ->count();
    }
}
