<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class FamilyGallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'family_id',
        'uploaded_by',
        'member_id',
        'title',
        'image',
        'description',
        'event_date',
        'created_by',
    ];

    public function family()
    {
        return $this->belongsTo(Family::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
