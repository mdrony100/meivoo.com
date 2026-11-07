<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Message extends Model
{
    protected static function booted(): void
    {
        static::saving(function (self $m) {
            $m->sender_user_id = $m->sender_user_id ?: $m->from_user_id;
            $m->from_user_id = $m->from_user_id ?: $m->sender_user_id;
            $m->body = $m->body ?: $m->message;
            $m->message = $m->message ?: $m->body;
        });
    }

    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_user_id',
        'message_type',
        'body',
        'conversation_id',
        'sender_user_id',
        'message_type',
        'body',
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
