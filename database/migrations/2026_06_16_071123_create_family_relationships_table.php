<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('family_relationships', function (Blueprint $table) {
            $table->id();

            $table->foreignId('family_id')->constrained()->onDelete('cascade');

            // Both names are stored. Older rows used person_id; the app
            // addresses the same people through member_id. Neither side is
            // required so a legacy row missing one name can be backfilled.
            $table->unsignedBigInteger('person_id')->nullable();
            $table->unsignedBigInteger('related_person_id')->nullable();
            $table->unsignedBigInteger('member_id')->nullable();
            $table->unsignedBigInteger('related_member_id')->nullable();

            $table->string('relation_type', 32)->nullable();
            $table->string('relationship_type', 32)->nullable();

            $table->date('marriage_date')->nullable();
            $table->date('anniversary_date')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_relationships');
    }
};
