<?php

namespace Tests\Feature;

use App\Database\AlignFamilyRelationships;
use App\Models\FamilyRelationship;
use App\Models\User;
use App\Support\MonthlyUserCounts;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\RichDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SafeLegacySchemaUpgradeTest extends TestCase
{
    private array $pending = [
        '2026_07_08_000000_create_personal_access_tokens_table',
        '2026_07_08_000001_create_refresh_tokens_table',
        '2026_07_08_000002_create_password_otps_table',
        '2026_07_13_100000_create_message_attachments_table',
        '2026_07_14_100000_create_events_table',
        '2026_07_14_100100_create_ai_photos_table',
        '2026_07_14_100200_align_family_relationship_columns',
        '2026_07_14_100300_rebuild_family_relationships_table',
        '2026_07_14_100400_align_messages_table',
        '2026_07_14_100500_create_family_trees_table',
        '2026_07_19_000000_create_legacy_names_table',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        RefreshDatabaseState::$migrated = false;
        Artisan::call('migrate:fresh', ['--force' => true]);
    }

    protected function tearDown(): void
    {
        RefreshDatabaseState::$migrated = false;
        Artisan::call('migrate:fresh', ['--force' => true]);

        parent::tearDown();
    }

    public function test_fresh_migrate_keeps_relationship_keys_and_syncs_both_column_names(): void
    {
        $this->assertFalse(Schema::hasTable('family_relationships_tmp'));

        foreach ([
            'member_id',
            'related_member_id',
            'person_id',
            'related_person_id',
            'relation_type',
            'relationship_type',
        ] as $column) {
            $this->assertTrue(Schema::hasColumn('family_relationships', $column), $column);
        }

        $this->assertNotNull($this->familyForeignKey());

        [$userId, $familyId, $memberA, $memberB] = $this->familyFixture();

        $relation = FamilyRelationship::create([
            'family_id' => $familyId,
            'member_id' => $memberA,
            'related_member_id' => $memberB,
            'relation_type' => 'father',
        ]);

        $row = DB::table('family_relationships')->where('id', $relation->id)->first();

        $this->assertSame($memberA, (int) $row->person_id);
        $this->assertSame($memberB, (int) $row->related_person_id);
        $this->assertSame('father', $row->relation_type);
        $this->assertSame((int) $relation->id, (int) $row->id);
        $this->assertNotNull($this->familyForeignKey());
    }

    public function test_live_shape_gains_person_columns_without_losing_rows_or_foreign_keys(): void
    {
        [$userId, $familyId, $memberA, $memberB] = $this->familyFixture();

        Schema::disableForeignKeyConstraints();
        Schema::drop('family_relationships');
        Schema::enableForeignKeyConstraints();

        Schema::create('family_relationships', function ($table) {
            $table->id();
            $table->foreignId('family_id')->constrained('families')->cascadeOnDelete();
            $table->unsignedBigInteger('member_id');
            $table->unsignedBigInteger('related_member_id');
            $table->string('relation_type', 32);
            $table->date('marriage_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $relationId = DB::table('family_relationships')->insertGetId([
            'family_id' => $familyId,
            'member_id' => $memberA,
            'related_member_id' => $memberB,
            'relation_type' => 'father',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $foreignKey = $this->familyForeignKey();
        $this->assertNotNull($foreignKey);

        $saved = FamilyRelationship::create([
            'family_id' => $familyId,
            'member_id' => $memberB,
            'related_member_id' => $memberA,
            'relation_type' => 'mother',
        ]);
        $this->assertFalse(Schema::hasColumn('family_relationships', 'person_id'));
        $this->assertNotNull($saved->id);

        DB::table('migrations')->whereIn('migration', [
            '2026_07_14_100200_align_family_relationship_columns',
            '2026_07_14_100300_rebuild_family_relationships_table',
        ])->delete();

        Artisan::call('migrate', ['--force' => true]);

        $this->assertFalse(Schema::hasTable('family_relationships_tmp'));
        $this->assertSame(2, DB::table('family_relationships')->count());

        $original = DB::table('family_relationships')->where('id', $relationId)->first();
        $this->assertNotNull($original);
        $this->assertSame($memberA, (int) $original->member_id);
        $this->assertSame($memberA, (int) $original->person_id);
        $this->assertSame($memberB, (int) $original->related_member_id);
        $this->assertSame($memberB, (int) $original->related_person_id);
        $this->assertSame('father', $original->relation_type);

        $created = DB::table('family_relationships')->where('id', $saved->id)->first();
        $this->assertSame($memberB, (int) $created->person_id);
        $this->assertSame('mother', $created->relation_type);

        $after = $this->familyForeignKey();
        $this->assertNotNull($after);
        $this->assertSame($foreignKey['columns'], $after['columns']);
        $this->assertSame($foreignKey['foreign_table'], $after['foreign_table']);
        if ($foreignKey['name'] !== null) {
            $this->assertSame($foreignKey['name'], $after['name']);
        }
    }

    public function test_original_enum_shape_is_widened_in_place_and_keeps_ids(): void
    {
        [$userId, $familyId, $memberA, $memberB] = $this->familyFixture();

        Schema::disableForeignKeyConstraints();
        Schema::drop('family_relationships');
        Schema::enableForeignKeyConstraints();

        $driver = Schema::getConnection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement("CREATE TABLE `family_relationships` (
                `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `family_id` BIGINT UNSIGNED NOT NULL,
                `person_id` BIGINT UNSIGNED NOT NULL,
                `related_person_id` BIGINT UNSIGNED NOT NULL,
                `relationship_type` ENUM('wife', 'husband') NOT NULL,
                `marriage_date` DATE NULL,
                `anniversary_date` DATE NULL,
                `is_active` TINYINT(1) NOT NULL DEFAULT 1,
                `created_at` TIMESTAMP NULL,
                `updated_at` TIMESTAMP NULL,
                CONSTRAINT `family_relationships_family_id_foreign` FOREIGN KEY (`family_id`) REFERENCES `families` (`id`) ON DELETE CASCADE
            )");
        } else {
            Schema::create('family_relationships', function ($table) {
                $table->id();
                $table->foreignId('family_id')->constrained('families')->cascadeOnDelete();
                $table->unsignedBigInteger('person_id');
                $table->unsignedBigInteger('related_person_id');
                $table->string('relationship_type', 32);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        DB::table('family_relationships')->insert([
            'id' => 7,
            'family_id' => $familyId,
            'person_id' => $memberA,
            'related_person_id' => $memberB,
            'relationship_type' => 'husband',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $beforeKey = $this->familyForeignKey();

        AlignFamilyRelationships::up();

        $row = DB::table('family_relationships')->where('id', 7)->first();
        $this->assertNotNull($row);
        $this->assertSame(1, DB::table('family_relationships')->count());
        $this->assertSame($memberA, (int) $row->person_id);
        $this->assertSame($memberA, (int) $row->member_id);
        $this->assertSame($memberB, (int) $row->related_person_id);
        $this->assertSame('husband', $row->relationship_type);
        $this->assertSame('spouse', $row->relation_type);
        $this->assertFalse(Schema::hasTable('family_relationships_tmp'));

        $afterKey = $this->familyForeignKey();
        $this->assertNotNull($beforeKey);
        $this->assertNotNull($afterKey);
        $this->assertSame($beforeKey['columns'], $afterKey['columns']);
        $this->assertSame($beforeKey['foreign_table'], $afterKey['foreign_table']);

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            $column = DB::selectOne(
                'SELECT IS_NULLABLE AS nullable, DATA_TYPE AS data_type
                 FROM information_schema.COLUMNS
                 WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
                ['family_relationships', 'relationship_type']
            );
            $this->assertSame('YES', strtoupper((string) $column->nullable));
            $this->assertSame('varchar', strtolower((string) $column->data_type));

            $created = FamilyRelationship::create([
                'family_id' => $familyId,
                'member_id' => $memberB,
                'related_member_id' => $memberA,
                'relation_type' => 'mother',
            ]);
            $this->assertSame(7, (int) DB::table('family_relationships')->min('id'));
            $this->assertNotSame(7, (int) $created->id);
        }
    }

    public function test_pending_migrations_adopt_existing_tables_and_rows(): void
    {
        [$userId, $familyId, $memberA, $memberB] = $this->familyFixture();

        $relationId = DB::table('family_relationships')->insertGetId([
            'family_id' => $familyId,
            'member_id' => $memberA,
            'related_member_id' => $memberB,
            'person_id' => $memberA,
            'related_person_id' => $memberB,
            'relation_type' => 'mother',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $tokenId = DB::table('personal_access_tokens')->insertGetId([
            'tokenable_type' => User::class,
            'tokenable_id' => $userId,
            'name' => 'phone',
            'token' => hash('sha256', 'kept-token'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $refreshId = DB::table('refresh_tokens')->insertGetId([
            'user_id' => $userId,
            'token_hash' => hash('sha256', 'kept-refresh'),
            'expires_at' => now()->addDay(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $otpId = DB::table('password_otps')->insertGetId([
            'email' => 'kept@example.com',
            'otp_hash' => hash('sha256', '123456'),
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $messageId = DB::table('messages')->insertGetId([
            'family_id' => $familyId,
            'from_user_id' => $userId,
            'to_user_id' => $userId,
            'message' => 'Keep this message',
            'is_read' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $attachmentId = DB::table('message_attachments')->insertGetId([
            'message_id' => $messageId,
            'path' => 'kept/photo.jpg',
            'type' => 'image',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $eventId = DB::table('events')->insertGetId([
            'family_id' => $familyId,
            'creator_user_id' => $userId,
            'type' => 'family_reunion',
            'title' => 'Kept reunion',
            'event_date' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $photoId = DB::table('ai_photos')->insertGetId([
            'user_id' => $userId,
            'tool_type' => 'restore',
            'status' => 'completed',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $treeId = DB::table('family_trees')->insertGetId([
            'owner_user_id' => $userId,
            'name' => 'Kept tree',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $legacyId = DB::table('legacy_names')->insertGetId([
            'family_id' => $familyId,
            'name' => 'Kept name',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Schema::drop('personal_access_tokens');
        Schema::create('personal_access_tokens', function ($table) {
            $table->id();
            $table->string('token', 64);
            $table->timestamps();
        });
        DB::table('personal_access_tokens')->insert([
            'id' => $tokenId,
            'token' => hash('sha256', 'kept-token'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('migrations')->whereIn('migration', $this->pending)->delete();

        Artisan::call('migrate', ['--force' => true]);

        $this->assertSame($relationId, (int) DB::table('family_relationships')->where('id', $relationId)->value('id'));
        $this->assertSame('mother', DB::table('family_relationships')->where('id', $relationId)->value('relation_type'));
        $this->assertSame($tokenId, (int) DB::table('personal_access_tokens')->where('token', hash('sha256', 'kept-token'))->value('id'));
        $this->assertTrue(Schema::hasColumn('personal_access_tokens', 'expires_at'));
        $this->assertSame($refreshId, (int) DB::table('refresh_tokens')->where('id', $refreshId)->value('id'));
        $this->assertSame($otpId, (int) DB::table('password_otps')->where('id', $otpId)->value('id'));
        $this->assertSame('Keep this message', DB::table('messages')->where('id', $messageId)->value('message'));
        $this->assertSame('Keep this message', DB::table('messages')->where('id', $messageId)->value('body'));
        $this->assertSame($userId, (int) DB::table('messages')->where('id', $messageId)->value('sender_user_id'));
        $this->assertSame($attachmentId, (int) DB::table('message_attachments')->where('id', $attachmentId)->value('id'));
        $this->assertSame('Kept reunion', DB::table('events')->where('id', $eventId)->value('title'));
        $this->assertSame($photoId, (int) DB::table('ai_photos')->where('id', $photoId)->value('id'));
        $this->assertSame('Kept tree', DB::table('family_trees')->where('id', $treeId)->value('name'));
        $this->assertSame('Kept name', DB::table('legacy_names')->where('id', $legacyId)->value('name'));

        AlignFamilyRelationships::down();
        $this->assertSame('Kept name', DB::table('legacy_names')->where('id', $legacyId)->value('name'));
        $this->assertTrue(Schema::hasTable('family_relationships'));
        $this->assertSame($relationId, (int) DB::table('family_relationships')->where('id', $relationId)->value('id'));
    }

    public function test_monthly_user_counts_use_mysql_month(): void
    {
        $source = file_get_contents(app_path('Http/Controllers/Admin/DashboardController.php'));
        $this->assertStringNotContainsString('strftime', $source);

        if (! in_array(Schema::getConnection()->getDriverName(), ['mysql', 'mariadb'], true)) {
            $this->markTestSkipped('MONTH() is exercised on MySQL. This process is using '.Schema::getConnection()->getDriverName().'.');
        }

        foreach ([
            ['January Member', 'jan@example.com', '2026-01-15 12:00:00'],
            ['January Member Two', 'jan2@example.com', '2026-01-20 12:00:00'],
            ['March Member', 'mar@example.com', '2026-03-02 12:00:00'],
        ] as [$name, $email, $created]) {
            DB::table('users')->insert([
                'name' => $name,
                'email' => $email,
                'password' => 'secret-secret',
                'created_at' => $created,
                'updated_at' => $created,
            ]);
        }

        $counts = MonthlyUserCounts::forYear(2026);

        $this->assertCount(12, $counts);
        $this->assertSame(2, $counts[0]);
        $this->assertSame(0, $counts[1]);
        $this->assertSame(1, $counts[2]);
    }

    public function test_demo_seeder_does_not_delete_an_existing_family_or_run_in_production(): void
    {
        [$userId, $familyId] = array_slice($this->familyFixture(), 0, 2);

        DB::table('families')->where('id', $familyId)->update(['slug' => 'rahman-family']);

        $this->seed(RichDemoSeeder::class);

        $this->assertSame($familyId, (int) DB::table('families')->where('slug', 'rahman-family')->value('id'));
        $this->assertSame(0, DB::table('family_relationships')->count());

        $this->app['env'] = 'production';
        (new DatabaseSeeder)->run();

        $this->assertSame(1, User::count());
        $this->assertSame($userId, (int) User::query()->value('id'));
    }

    public function test_alignment_source_does_not_drop_or_rename_the_table(): void
    {
        $files = [
            database_path('migrations/2026_07_14_100300_rebuild_family_relationships_table.php'),
            app_path('Database/AlignFamilyRelationships.php'),
        ];

        foreach ($files as $file) {
            $source = file_get_contents($file);
            $this->assertDoesNotMatchRegularExpression('/Schema::(drop|dropIfExists|rename)\s*\(/', $source);
            $this->assertStringNotContainsString('family_relationships_tmp', $source);
        }
    }

    /**
     * @return array{0: int, 1: int, 2: int, 3: int}
     */
    private function familyFixture(): array
    {
        $userId = DB::table('users')->insertGetId([
            'name' => 'Keeper',
            'email' => 'keeper@example.com',
            'password' => 'secret-secret',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $familyId = DB::table('families')->insertGetId([
            'name' => 'Kept Family',
            'slug' => 'kept-family',
            'created_by' => $userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $memberA = DB::table('family_members')->insertGetId([
            'family_id' => $familyId,
            'full_name' => 'Parent',
            'gender' => 'male',
            'status' => 'alive',
            'generation' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $memberB = DB::table('family_members')->insertGetId([
            'family_id' => $familyId,
            'full_name' => 'Child',
            'gender' => 'female',
            'status' => 'alive',
            'generation' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [$userId, $familyId, $memberA, $memberB];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function familyForeignKey(): ?array
    {
        foreach (Schema::getForeignKeys('family_relationships') as $key) {
            if (in_array('family_id', $key['columns'], true) && $key['foreign_table'] === 'families') {
                return $key;
            }
        }

        return null;
    }
}
