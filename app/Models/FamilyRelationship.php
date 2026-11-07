<?php

namespace App\Models;

use App\Models\Concerns\GuardsMissingColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyRelationship extends Model
{
    use GuardsMissingColumns;
    use HasFactory;

    protected static function booted(): void
    {
        static::saving(function (self $rel) {
            $rel->mirrorColumns([
                ['member_id', 'person_id'],
                ['related_member_id', 'related_person_id'],
            ]);
        });
    }

    protected $fillable = [
        'family_id',
        'member_id',
        'related_member_id',
        'person_id',
        'related_person_id',
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
