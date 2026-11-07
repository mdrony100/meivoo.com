<?php

use App\Database\SafeSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('refresh_tokens')) {
            Schema::create('refresh_tokens', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->unsignedBigInteger('sanctum_token_id')->nullable()->index();
                $table->string('token_hash', 128)->unique();
                $table->timestamp('expires_at')->index();
                $table->timestamp('revoked_at')->nullable()->index();
                $table->timestamp('last_used_at')->nullable();
                $table->foreignId('replaced_by_refresh_token_id')->nullable()->index();
                $table->string('device_name')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();
            });

            return;
        }

        SafeSchema::addMissingColumns('refresh_tokens', [
            'user_id' => fn (Blueprint $table) => $table->unsignedBigInteger('user_id')->nullable(),
            'sanctum_token_id' => fn (Blueprint $table) => $table->unsignedBigInteger('sanctum_token_id')->nullable(),
            'token_hash' => fn (Blueprint $table) => $table->string('token_hash', 128)->nullable(),
            'expires_at' => fn (Blueprint $table) => $table->timestamp('expires_at')->nullable(),
            'revoked_at' => fn (Blueprint $table) => $table->timestamp('revoked_at')->nullable(),
            'last_used_at' => fn (Blueprint $table) => $table->timestamp('last_used_at')->nullable(),
            'replaced_by_refresh_token_id' => fn (Blueprint $table) => $table->unsignedBigInteger('replaced_by_refresh_token_id')->nullable(),
            'device_name' => fn (Blueprint $table) => $table->string('device_name')->nullable(),
            'ip_address' => fn (Blueprint $table) => $table->string('ip_address', 45)->nullable(),
            'user_agent' => fn (Blueprint $table) => $table->text('user_agent')->nullable(),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
        ]);
    }

    public function down(): void
    {
        // Forward-only. An existing refresh_tokens table must survive rollback.
    }
};
