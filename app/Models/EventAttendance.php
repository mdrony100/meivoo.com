<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'status',
    ];

    public const STATUS_GOING = 'going';
    public const STATUS_MAYBE = 'maybe';
    public const STATUS_NO = 'no';

    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_GOING,
            self::STATUS_MAYBE,
            self::STATUS_NO,
        ];
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}