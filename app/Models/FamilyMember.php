<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'family_id',
        'user_id',
        'full_name',
        'gender',
        'birth_date',
        'occupation',
        'generation',
        'status',
        'death_date',
        'parent_id',
        'is_self',
        'phone',
        'created_by',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'death_date' => 'date',
    ];

    /** Legacy views read these names; keep them working. */
    public function getProfessionAttribute(): ?string
    {
        return $this->occupation;
    }

    public function getDateOfDeathAttribute()
    {
        return $this->death_date;
    }

    public function family()
    {
        return $this->belongsTo(Family::class, 'family_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    // relationships where this member is source
    public function relationships()
    {
        return $this->hasMany(FamilyRelationship::class, 'member_id');
    }

    // relationships where this member is target
    public function relatedTo()
    {
        return $this->hasMany(FamilyRelationship::class, 'related_member_id');
    }

    // 👇 helpers for tree

    public function children()
    {
        return $this->belongsToMany(
            FamilyMember::class,
            'family_relationships',
            'member_id',
            'related_member_id'
        )->wherePivotIn('relation_type', ['father', 'mother']);
    }

    public function spouses()
    {
        return $this->belongsToMany(
            FamilyMember::class,
            'family_relationships',
            'member_id',
            'related_member_id'
        )->wherePivot('relation_type', 'spouse');
    }

    public function parents()
    {
        return $this->belongsToMany(
            FamilyMember::class,
            'family_relationships',
            'related_member_id',
            'member_id'
        )->wherePivotIn('relation_type', ['father', 'mother']);
    }

    public function father()
    {
        return $this->hasOne(FamilyRelationship::class, 'member_id')
            ->where('relation_type', 'father');
    }

    public function mother()
    {
        return $this->hasOne(FamilyRelationship::class, 'member_id')
            ->where('relation_type', 'mother');
    }

    public function spouse()
    {
        return $this->hasOne(FamilyRelationship::class, 'member_id')
            ->where('relation_type', 'spouse');
    }

    public function getTopParent()
    {
        $parentRelation = FamilyRelationship::where('related_member_id', $this->id)
            ->whereIn('relation_type', ['father', 'mother'])
            ->first();

        if ($parentRelation) {
            $parent = FamilyMember::find($parentRelation->member_id);

            return $parent ? $parent->getTopParent() : $this;
        }

        return $this;
    }
}
