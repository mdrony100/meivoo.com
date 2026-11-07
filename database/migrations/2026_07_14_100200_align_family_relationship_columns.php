<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The controllers and models address relationship rows through
 * member_id / related_member_id, while the original migration created
 * person_id / related_person_id. Add the columns the code expects and
 * backfill them so both naming styles keep working.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('family_relationships', function (Blueprint $table) {
            if (! Schema::hasColumn('family_relationships', 'member_id')) {
                $table->unsignedBigInteger('member_id')->nullable()->after('family_id')->index();
            }
            if (! Schema::hasColumn('family_relationships', 'related_member_id')) {
                $table->unsignedBigInteger('related_member_id')->nullable()->after('member_id')->index();
            }
        });

        if (Schema::hasColumn('family_relationships', 'person_id')) {
            \Illuminate\Support\Facades\DB::table('family_relationships')
                ->whereNull('member_id')
                ->update(['member_id' => \Illuminate\Support\Facades\DB::raw('person_id')]);
        }

        if (Schema::hasColumn('family_relationships', 'related_person_id')) {
            \Illuminate\Support\Facades\DB::table('family_relationships')
                ->whereNull('related_member_id')
                ->update(['related_member_id' => \Illuminate\Support\Facades\DB::raw('related_person_id')]);
        }
    }

    public function down(): void
    {
        Schema::table('family_relationships', function (Blueprint $table) {
            $table->dropColumn(['member_id', 'related_member_id']);
        });
    }
};
