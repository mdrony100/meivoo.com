<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();

             $table->integer('family_id')->nullable();
            // Sender
            $table->foreignId('from_user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Receiver
            $table->foreignId('to_user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Message text
            $table->text('message');

            // Read status
            $table->boolean('is_read')->default(false);

            $table->timestamp('read_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};