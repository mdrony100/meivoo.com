<?php

namespace App\Database;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Add the conversation-style message columns without touching existing
 * from_user_id / message values. Null targets are copied from the legacy
 * columns; non-null values are left as they are.
 */
class AlignMessages
{
    public static function up(): void
    {
        if (! Schema::hasTable('messages')) {
            return;
        }

        SafeSchema::addMissingColumns('messages', [
            'conversation_id' => fn (Blueprint $table) => $table->unsignedBigInteger('conversation_id')->nullable(),
            'sender_user_id' => fn (Blueprint $table) => $table->unsignedBigInteger('sender_user_id')->nullable(),
            'message_type' => fn (Blueprint $table) => $table->string('message_type')->default('text'),
            'body' => fn (Blueprint $table) => $table->text('body')->nullable(),
        ]);

        if (Schema::hasColumn('messages', 'body') && Schema::hasColumn('messages', 'message')) {
            DB::table('messages')
                ->whereNull('body')
                ->whereNotNull('message')
                ->update(['body' => DB::raw('message')]);
        }

        if (Schema::hasColumn('messages', 'sender_user_id') && Schema::hasColumn('messages', 'from_user_id')) {
            DB::table('messages')
                ->whereNull('sender_user_id')
                ->whereNotNull('from_user_id')
                ->update(['sender_user_id' => DB::raw('from_user_id')]);
        }
    }

    public static function down(): void
    {
        // Forward-only. These columns may hold the only copy of a message body.
    }
}
