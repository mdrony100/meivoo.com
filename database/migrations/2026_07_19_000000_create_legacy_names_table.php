<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('legacy_names', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_id')->constrained('families')->onDelete('cascade');
            $table->string('name');
            $table->text('meaning')->nullable();
            $table->text('story_behind_the_name')->nullable();
            $table->string('who_gave_this_name')->nullable();
            $table->string('family_generation')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('legacy_names');
    }
};