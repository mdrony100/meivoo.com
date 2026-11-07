<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Memory extends Model
{
    use HasFactory;

    protected $fillable = [
        'family_tree_id',
        'author_user_id',
        'family_member_id',
        'memory_type',
        'title',
        'content',
        'voice_path',
        'voice_duration',
        'visibility',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'voice_duration' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    public function familyTree(): BelongsTo
    {
        return $this->belongsTo(FamilyTree::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_user_id');
    }

    public function familyMember(): BelongsTo
    {
        return $this->belongsTo(FamilyMember::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(MemoryAttachment::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(MemoryLike::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(MemoryComment::class);
    }
}
