<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Family extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'owner_user_id'
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function members()
    {
        return $this->hasMany(FamilyMember::class, 'family_id');
    }

    public function relationships()
    {
        return $this->hasMany(FamilyRelationship::class, 'family_id');
    }
}
