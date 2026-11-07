<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'family_id',
        'creator_user_id',
        'type',
        'title',
        'description',
        'event_date',
        'location',
        'cover_image',
    ];

    protected $casts = [
        'event_date' => 'datetime',
    ];

    public const TYPE_BIRTHDAY = 'birthday';
    public const TYPE_DEATH_ANNIVERSARY = 'death_anniversary';
    public const TYPE_MARRIAGE_ANNIVERSARY = 'marriage_anniversary';
    public const TYPE_MARRIAGE = 'marriage';
    public const TYPE_FAMILY_REUNION = 'family_reunion';

    public static function getTypes(): array
    {
        return [
            self::TYPE_BIRTHDAY,
            self::TYPE_DEATH_ANNIVERSARY,
            self::TYPE_MARRIAGE_ANNIVERSARY,
            self::TYPE_MARRIAGE,
            self::TYPE_FAMILY_REUNION,
        ];
    }

    public function family()
    {
        return $this->belongsTo(Family::class, 'family_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_user_id');
    }

    public function attendances()
    {
        return $this->hasMany(EventAttendance::class, 'event_id');
    }

    public function goingMembers()
    {
        return $this->belongsToMany(
            User::class,
            'event_attendances',
            'event_id',
            'user_id'
        )->wherePivot('status', 'going');
    }

    public function maybeMembers()
    {
        return $this->belongsToMany(
            User::class,
            'event_attendances',
            'event_id',
            'user_id'
        )->wherePivot('status', 'maybe');
    }

    public function noMembers()
    {
        return $this->belongsToMany(
            User::class,
            'event_attendances',
            'event_id',
            'user_id'
        )->wherePivot('status', 'no');
    }
}