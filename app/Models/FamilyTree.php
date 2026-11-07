<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FamilyTree extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_user_id',
        'name',
        'description',
        'cover_image',
        'visibility',
        'status',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(FamilyMember::class);
    }

    public function memories(): HasMany
    {
        return $this->hasMany(Memory::class);
    }
}
