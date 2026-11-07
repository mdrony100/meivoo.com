<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiPhoto extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'tool_type',
        'original_path',
        'enhanced_path',
        'status',
        'error_message',
    ];

    /**
     * Get the user that owns the enhanced photo.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}