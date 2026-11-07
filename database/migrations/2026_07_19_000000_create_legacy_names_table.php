<?php

use App\Database\SafeSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('legacy_names')) {
            Schema::create('legacy_names', function (Blueprint $table) {
                $table->id();
                $table->foreignId('family_id')->constrained('families')->onDelete('cascade');
                $table->string('name');
                $table->text('meaning')->nullable();
                $table->text('story_behind_the_name')->nullable();
                $table->string('who_gave_this_name')->nullable();
                $table->string('family_generation')->nullable();
                $table->timestamps();
            });

            return;
        }

        SafeSchema::addMissingColumns('legacy_names', [
            'family_id' => fn (Blueprint $table) => $table->unsignedBigInteger('family_id')->nullable(),
            'name' => fn (Blueprint $table) => $table->string('name')->nullable(),
            'meaning' => fn (Blueprint $table) => $table->text('meaning')->nullable(),
            'story_behind_the_name' => fn (Blueprint $table) => $table->text('story_behind_the_name')->nullable(),
            'who_gave_this_name' => fn (Blueprint $table) => $table->string('who_gave_this_name')->nullable(),
            'family_generation' => fn (Blueprint $table) => $table->string('family_generation')->nullable(),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
        ]);
    }

    public function down(): void
    {
        // Forward-only. Existing legacy names must not be dropped.
    }
};
