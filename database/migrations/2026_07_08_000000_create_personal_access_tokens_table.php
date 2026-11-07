<?php

use App\Database\SafeSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('personal_access_tokens')) {
            Schema::create('personal_access_tokens', function (Blueprint $table) {
                $table->id();
                $table->morphs('tokenable');
                $table->string('name');
                $table->string('token', 64)->unique();
                $table->text('abilities')->nullable();
                $table->timestamp('last_used_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });

            return;
        }

        SafeSchema::addMissingColumns('personal_access_tokens', [
            'tokenable_type' => fn (Blueprint $table) => $table->string('tokenable_type')->nullable(),
            'tokenable_id' => fn (Blueprint $table) => $table->unsignedBigInteger('tokenable_id')->nullable(),
            'name' => fn (Blueprint $table) => $table->string('name')->nullable(),
            'token' => fn (Blueprint $table) => $table->string('token', 64)->nullable(),
            'abilities' => fn (Blueprint $table) => $table->text('abilities')->nullable(),
            'last_used_at' => fn (Blueprint $table) => $table->timestamp('last_used_at')->nullable(),
            'expires_at' => fn (Blueprint $table) => $table->timestamp('expires_at')->nullable(),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
        ]);
    }

    public function down(): void
    {
        // The live table can already exist when this migration is recorded.
        // Rolling it back must not drop those tokens.
    }
};
