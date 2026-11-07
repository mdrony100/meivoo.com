<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'display_name',
        'avatar_path',
        'cover_path',
        'phone',
        'location',
        'biography',
        'birth_date',
        'member_since',
        'timezone',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'member_since' => 'date',
            'settings' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
