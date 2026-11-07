<?php

use App\Database\AlignFamilyRelationships;
use Illuminate\Database\Migrations\Migration;

/**
 * Add whichever relationship column names are missing and copy null pairs.
 * This does not drop or rebuild family_relationships.
 */
return new class extends Migration
{
    public function up(): void
    {
        AlignFamilyRelationships::up();
    }

    public function down(): void
    {
        AlignFamilyRelationships::down();
    }
};
