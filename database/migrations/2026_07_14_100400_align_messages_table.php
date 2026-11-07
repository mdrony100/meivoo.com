<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            if (! Schema::hasColumn('messages', 'conversation_id')) {
                $table->unsignedBigInteger('conversation_id')->nullable()->index();
            }
            if (! Schema::hasColumn('messages', 'sender_user_id')) {
                $table->unsignedBigInteger('sender_user_id')->nullable()->index();
            }
            if (! Schema::hasColumn('messages', 'message_type')) {
                $table->string('message_type')->default('text');
            }
            if (! Schema::hasColumn('messages', 'body')) {
                $table->text('body')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['conversation_id', 'sender_user_id', 'message_type', 'body']);
        });
    }
};
