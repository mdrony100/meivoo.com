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

            $table->unsignedBigInteger('person_id');
            $table->unsignedBigInteger('related_person_id');

            $table->enum('relationship_type', ['wife', 'husband']);

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
