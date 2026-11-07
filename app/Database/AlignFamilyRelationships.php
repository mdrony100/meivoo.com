<?php

namespace App\Database;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Bring family_relationships up to the columns the application reads and
 * writes, without replacing the table.
 *
 * Live MySQL already has relation_type / member_id and is missing
 * person_id / related_person_id. A fresh database created by the original
 * migration has the opposite pair, plus a wife|husband enum. Both shapes are
 * repaired in place: missing columns are added, null identifier pairs are
 * copied across, and the legacy enum is widened only when it would reject
 * the relation values the app stores. Primary keys and foreign keys stay.
 */
class AlignFamilyRelationships
{
    public static function up(): void
    {
        if (! Schema::hasTable('family_relationships')) {
            return;
        }

        SafeSchema::addMissingColumns('family_relationships', [
            'member_id' => fn (Blueprint $table) => $table->unsignedBigInteger('member_id')->nullable(),
            'related_member_id' => fn (Blueprint $table) => $table->unsignedBigInteger('related_member_id')->nullable(),
            'person_id' => fn (Blueprint $table) => $table->unsignedBigInteger('person_id')->nullable(),
            'related_person_id' => fn (Blueprint $table) => $table->unsignedBigInteger('related_person_id')->nullable(),
            'relation_type' => fn (Blueprint $table) => $table->string('relation_type', 32)->nullable(),
            'relationship_type' => fn (Blueprint $table) => $table->string('relationship_type', 32)->nullable(),
            'marriage_date' => fn (Blueprint $table) => $table->date('marriage_date')->nullable(),
            'anniversary_date' => fn (Blueprint $table) => $table->date('anniversary_date')->nullable(),
            'is_active' => fn (Blueprint $table) => $table->boolean('is_active')->default(true),
        ]);

        self::relaxRelationshipType();
        self::backfillIdentifiers();
    }

    public static function down(): void
    {
        // Forward-only. Dropping these columns would discard the only copy of
        // a relationship on databases that never had the other name.
    }

    private static function relaxRelationshipType(): void
    {
        if (! Schema::hasColumn('family_relationships', 'relationship_type')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        $column = DB::selectOne(
            'SELECT IS_NULLABLE AS nullable, DATA_TYPE AS data_type, COLUMN_TYPE AS column_type
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?',
            ['family_relationships', 'relationship_type']
        );

        if ($column === null) {
            return;
        }

        $columnType = strtolower((string) $column->column_type);
        $isEnum = str_starts_with($columnType, 'enum');
        $notNull = strtoupper((string) $column->nullable) === 'NO';
        $isVarchar = strtolower((string) $column->data_type) === 'varchar';

        if (! $isEnum && ! $notNull && $isVarchar) {
            return;
        }

        DB::statement('ALTER TABLE `family_relationships` MODIFY `relationship_type` VARCHAR(32) NULL');
    }

    private static function backfillIdentifiers(): void
    {
        $has = fn (string $column): bool => Schema::hasColumn('family_relationships', $column);

        if ($has('member_id') && $has('person_id')) {
            DB::table('family_relationships')
                ->whereNull('member_id')
                ->whereNotNull('person_id')
                ->update(['member_id' => DB::raw('person_id')]);

            DB::table('family_relationships')
                ->whereNull('person_id')
                ->whereNotNull('member_id')
                ->update(['person_id' => DB::raw('member_id')]);
        }

        if ($has('related_member_id') && $has('related_person_id')) {
            DB::table('family_relationships')
                ->whereNull('related_member_id')
                ->whereNotNull('related_person_id')
                ->update(['related_member_id' => DB::raw('related_person_id')]);

            DB::table('family_relationships')
                ->whereNull('related_person_id')
                ->whereNotNull('related_member_id')
                ->update(['related_person_id' => DB::raw('related_member_id')]);
        }

        if ($has('relation_type') && $has('relationship_type')) {
            DB::table('family_relationships')
                ->whereNull('relation_type')
                ->whereIn('relationship_type', ['husband', 'wife', 'spouse', 'father', 'mother', 'child', 'sibling'])
                ->update([
                    'relation_type' => DB::raw("CASE relationship_type WHEN 'husband' THEN 'spouse' WHEN 'wife' THEN 'spouse' ELSE relationship_type END"),
                ]);
        }
    }
}
