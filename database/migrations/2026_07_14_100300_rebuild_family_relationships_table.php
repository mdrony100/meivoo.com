<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The application code addresses family relationships through
 * member_id / related_member_id / relation_type (122 references), while the
 * original table shipped person_id / related_person_id / relationship_type
 * with a husband|wife check constraint. Rebuild the table once, keeping both
 * column families so older API code and the newer controllers agree.
 *
 * MySQL is the supported driver, so this uses the schema builder and
 * Schema::getColumnListing() rather than SQLite's PRAGMA / AUTOINCREMENT.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('family_relationships')) {
            return;
        }

        if (in_array('relation_type', Schema::getColumnListing('family_relationships'), true)) {
            return;
        }

        Schema::dropIfExists('family_relationships_tmp');

        $sql = Schema::getConnection()->getDriverName() === 'mysql'
            ? "CREATE TABLE `family_relationships_tmp` (
                 `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                 `family_id` BIGINT UNSIGNED NOT NULL,
                 `member_id` BIGINT UNSIGNED NULL,
                 `related_member_id` BIGINT UNSIGNED NULL,
                 `person_id` BIGINT UNSIGNED NULL,
                 `related_person_id` BIGINT UNSIGNED NULL,
                 `relation_type` VARCHAR(32) NOT NULL DEFAULT 'spouse',
                 `relationship_type` VARCHAR(32) NULL,
                 `marriage_date` DATE NULL,
                 `anniversary_date` DATE NULL,
                 `is_active` TINYINT(1) NOT NULL DEFAULT 1,
                 `created_at` TIMESTAMP NULL,
                 `updated_at` TIMESTAMP NULL,
                 KEY `fr_family_idx` (`family_id`),
                 KEY `fr_member_idx` (`member_id`),
                 KEY `fr_related_idx` (`related_member_id`)
               ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
            : null;

        if ($sql !== null) {
            DB::statement($sql);
        } else {
            Schema::create('family_relationships_tmp', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('family_id');
                $table->unsignedBigInteger('member_id')->nullable();
                $table->unsignedBigInteger('related_member_id')->nullable();
                $table->unsignedBigInteger('person_id')->nullable();
                $table->unsignedBigInteger('related_person_id')->nullable();
                $table->string('relation_type', 32)->default('spouse');
                $table->string('relationship_type', 32)->nullable();
                $table->date('marriage_date')->nullable();
                $table->date('anniversary_date')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->index('family_id', 'fr_family_idx');
                $table->index('member_id', 'fr_member_idx');
                $table->index('related_member_id', 'fr_related_idx');
            });
        }

        DB::table('family_relationships')->orderBy('id')->chunk(500, function ($rows) {
            $payload = [];

            foreach ($rows as $row) {
                $row = (array) $row;
                $legacy = $row['relationship_type'] ?? null;

                $type = match ($legacy) {
                    'husband', 'wife', 'spouse' => 'spouse',
                    'father', 'mother', 'child', 'sibling' => $legacy,
                    default => 'spouse',
                };

                $payload[] = [
                    'family_id' => $row['family_id'],
                    'member_id' => $row['member_id'] ?? $row['person_id'] ?? null,
                    'related_member_id' => $row['related_member_id'] ?? $row['related_person_id'] ?? null,
                    'person_id' => $row['person_id'] ?? $row['member_id'] ?? null,
                    'related_person_id' => $row['related_person_id'] ?? $row['related_member_id'] ?? null,
                    'relation_type' => $type,
                    'relationship_type' => $legacy,
                    'marriage_date' => $row['marriage_date'] ?? null,
                    'anniversary_date' => $row['anniversary_date'] ?? null,
                    'is_active' => $row['is_active'] ?? 1,
                    'created_at' => $row['created_at'] ?? now(),
                    'updated_at' => $row['updated_at'] ?? now(),
                ];
            }

            if ($payload !== []) {
                DB::table('family_relationships_tmp')->insert($payload);
            }
        });

        Schema::drop('family_relationships');
        Schema::rename('family_relationships_tmp', 'family_relationships');
    }

    public function down(): void
    {
        // Forward-only compatibility repair; nothing to roll back safely.
    }
};
