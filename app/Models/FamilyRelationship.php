<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyRelationship extends Model
{
    protected static function booted(): void
    {
        static::saving(function (self $rel) {
            $rel->member_id = $rel->member_id ?: $rel->person_id;
            $rel->related_member_id = $rel->related_member_id ?: $rel->related_person_id;
            $rel->person_id = $rel->person_id ?: $rel->member_id;
            $rel->related_person_id = $rel->related_person_id ?: $rel->related_member_id;
        });
    }

    use HasFactory;

    protected $fillable = [
        'member_id',
        'related_member_id',
        'family_id',
        'member_id',
        'related_member_id',
        'relation_type',
    ];

    public function family()
    {
        return $this->belongsTo(Family::class, 'family_id');
    }

    public function member()
    {
        return $this->belongsTo(FamilyMember::class, 'member_id');
    }

    public function relatedMember()
    {
        return $this->belongsTo(FamilyMember::class, 'related_member_id');
    }

}
