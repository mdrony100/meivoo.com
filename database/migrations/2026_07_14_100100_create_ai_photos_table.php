<?php

use App\Database\SafeSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ai_photos')) {
            Schema::create('ai_photos', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->string('tool_type')->nullable();
                $table->string('original_path')->nullable();
                $table->string('enhanced_path')->nullable();
                $table->string('status')->default('pending');
                $table->text('error_message')->nullable();
                $table->timestamps();
            });

            return;
        }

        SafeSchema::addMissingColumns('ai_photos', [
            'user_id' => fn (Blueprint $table) => $table->unsignedBigInteger('user_id')->nullable(),
            'tool_type' => fn (Blueprint $table) => $table->string('tool_type')->nullable(),
            'original_path' => fn (Blueprint $table) => $table->string('original_path')->nullable(),
            'enhanced_path' => fn (Blueprint $table) => $table->string('enhanced_path')->nullable(),
            'status' => fn (Blueprint $table) => $table->string('status')->default('pending'),
            'error_message' => fn (Blueprint $table) => $table->text('error_message')->nullable(),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
        ]);
    }

    public function down(): void
    {
        // Forward-only. Existing restored photos must not be dropped.
    }
};
