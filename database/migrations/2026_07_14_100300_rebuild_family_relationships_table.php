<?php

use App\Database\AlignFamilyRelationships;
use Illuminate\Database\Migrations\Migration;

/**
 * Previously this migration copied family_relationships into a new table,
 * dropped the original, and renamed the copy. That discarded primary keys
 * and foreign keys, and it returned early when relation_type already existed,
 * which is the live MySQL shape — so person_id was never added.
 *
 * The filename is unchanged so databases that already recorded this migration
 * are not asked to run a new one. The work is now the same in-place alignment
 * as 100200 and is safe to run again.
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
