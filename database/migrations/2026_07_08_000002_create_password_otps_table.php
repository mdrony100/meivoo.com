<?php

use App\Database\SafeSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('password_otps')) {
            Schema::create('password_otps', function (Blueprint $table) {
                $table->id();
                $table->string('email')->index();
                $table->string('otp_hash', 128);
                $table->timestamp('expires_at')->index();
                $table->timestamp('verified_at')->nullable()->index();
                $table->timestamp('invalidated_at')->nullable()->index();
                $table->timestamp('used_at')->nullable()->index();
                $table->unsignedSmallInteger('attempts')->default(0);
                $table->timestamps();
            });

            return;
        }

        SafeSchema::addMissingColumns('password_otps', [
            'email' => fn (Blueprint $table) => $table->string('email')->nullable(),
            'otp_hash' => fn (Blueprint $table) => $table->string('otp_hash', 128)->nullable(),
            'expires_at' => fn (Blueprint $table) => $table->timestamp('expires_at')->nullable(),
            'verified_at' => fn (Blueprint $table) => $table->timestamp('verified_at')->nullable(),
            'invalidated_at' => fn (Blueprint $table) => $table->timestamp('invalidated_at')->nullable(),
            'used_at' => fn (Blueprint $table) => $table->timestamp('used_at')->nullable(),
            'attempts' => fn (Blueprint $table) => $table->unsignedSmallInteger('attempts')->default(0),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
        ]);
    }

    public function down(): void
    {
        // Forward-only. Existing one-time passwords must not be dropped.
    }
};
