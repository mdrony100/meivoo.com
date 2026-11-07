<?php

namespace App\Models;

use App\Models\Concerns\GuardsMissingColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Message extends Model
{
    use GuardsMissingColumns;
    use HasFactory;

    protected static function booted(): void
    {
        static::saving(function (self $m) {
            $m->mirrorColumns([
                ['sender_user_id', 'from_user_id'],
                ['body', 'message'],
            ]);
        });
    }

    protected $fillable = [
        'conversation_id',
        'sender_user_id',
        'from_user_id',
        'to_user_id',
        'message_type',
        'body',
        'message',
        'family_id',
        'is_read',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(MessageAttachment::class);
    }
}
