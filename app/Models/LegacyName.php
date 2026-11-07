<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegacyName extends Model
{
    use HasFactory;

    protected $fillable = [
        'family_id',
        'name',
        'meaning',
        'story_behind_the_name',
        'who_gave_this_name',
        'family_generation',
    ];

    // public function family()
    // {
    //     return $this->belongsTo(Family::class);
    // }

    public function family()
    {
        return $this->belongsTo(Family::class, 'family_id');
    }
}
