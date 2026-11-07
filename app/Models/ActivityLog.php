<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public const CATEGORY_AUTH = 'auth';

    protected $fillable = [
        'category',
        'event',
        'status',
        'message',
        'user_id',
        'user_email',
        'user_name',
    ];

    public static function log(
        string $category,
        string $event,
        string $status,
        string $message,
        ?int $userId = null,
        ?string $userEmail = null,
        ?string $userName = null,
    ): void {
        try {
            static::create([
                'category' => $category,
                'event' => $event,
                'status' => $status,
                'message' => $message,
                'user_id' => $userId,
                'user_email' => $userEmail,
                'user_name' => $userName,
            ]);
        } catch (\Throwable $e) {
            return;
        }
    }
}
