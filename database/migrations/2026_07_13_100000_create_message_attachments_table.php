<?php

use App\Database\SafeSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('message_attachments')) {
            Schema::create('message_attachments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('message_id')->constrained()->cascadeOnDelete();
                $table->string('path');
                $table->enum('type', ['image', 'audio', 'video', 'file', 'voice'])->default('file');
                $table->timestamps();
            });

            return;
        }

        SafeSchema::addMissingColumns('message_attachments', [
            'message_id' => fn (Blueprint $table) => $table->unsignedBigInteger('message_id')->nullable(),
            'path' => fn (Blueprint $table) => $table->string('path')->nullable(),
            'type' => fn (Blueprint $table) => $table->string('type')->default('file'),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
        ]);
    }

    public function down(): void
    {
        // Forward-only. Existing attachments must not be dropped.
    }
};
