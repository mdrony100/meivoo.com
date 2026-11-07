<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('designation');
            $table->string('company_name')->nullable();
            $table->unsignedInteger('order_id')->default(0);
            $table->longText('details');
            $table->boolean('status')->default(true);
            $table->string('image')->nullable();
            $table->unsignedTinyInteger('rates')->default(5);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
