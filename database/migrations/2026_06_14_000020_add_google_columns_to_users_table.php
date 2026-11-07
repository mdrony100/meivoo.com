<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'google_id')) {
                $table->string('google_id')->nullable()->unique()->after('email');
            }

            if (! Schema::hasColumn('users', 'google_email')) {
                $table->string('google_email')->nullable()->after('google_id');
            }

        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
      

            if (Schema::hasColumn('users', 'google_email')) {
                $table->dropColumn('google_email');
            }

            if (Schema::hasColumn('users', 'google_id')) {
                $table->dropUnique('users_google_id_unique');
                $table->dropColumn('google_id');
            }
        });
    }
};
