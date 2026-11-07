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
        Schema::create('family_members', function (Blueprint $table) {
            $table->id();

            $table->foreignId('family_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('full_name');
            $table->string('gender')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('occupation')->nullable();
            $table->enum('status', ['alive', 'passed'])->default('alive');
            $table->date('death_date')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->integer('generation')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_members');
    }
};
