<?php

use App\Database\SafeSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('events')) {
            Schema::create('events', function (Blueprint $table) {
                $table->id();
                $table->foreignId('family_id')->nullable()->constrained('families')->cascadeOnDelete();
                $table->foreignId('creator_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('type')->default('family_reunion');
                $table->string('title');
                $table->text('description')->nullable();
                $table->dateTime('event_date')->nullable();
                $table->string('location')->nullable();
                $table->string('cover_image')->nullable();
                $table->timestamps();

                $table->index(['family_id', 'event_date']);
            });
        } else {
            SafeSchema::addMissingColumns('events', [
                'family_id' => fn (Blueprint $table) => $table->unsignedBigInteger('family_id')->nullable(),
                'creator_user_id' => fn (Blueprint $table) => $table->unsignedBigInteger('creator_user_id')->nullable(),
                'type' => fn (Blueprint $table) => $table->string('type')->default('family_reunion'),
                'title' => fn (Blueprint $table) => $table->string('title')->nullable(),
                'description' => fn (Blueprint $table) => $table->text('description')->nullable(),
                'event_date' => fn (Blueprint $table) => $table->dateTime('event_date')->nullable(),
                'location' => fn (Blueprint $table) => $table->string('location')->nullable(),
                'cover_image' => fn (Blueprint $table) => $table->string('cover_image')->nullable(),
                'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
                'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
            ]);
        }

        if (! Schema::hasTable('event_attendances')) {
            Schema::create('event_attendances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('status')->default('going');
                $table->timestamps();

                $table->unique(['event_id', 'user_id']);
            });
        } else {
            SafeSchema::addMissingColumns('event_attendances', [
                'event_id' => fn (Blueprint $table) => $table->unsignedBigInteger('event_id')->nullable(),
                'user_id' => fn (Blueprint $table) => $table->unsignedBigInteger('user_id')->nullable(),
                'status' => fn (Blueprint $table) => $table->string('status')->default('going'),
                'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
                'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
            ]);
        }
    }

    public function down(): void
    {
        // Forward-only. events and event_attendances may already hold rows.
    }
};
