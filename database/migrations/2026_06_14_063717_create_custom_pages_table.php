<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_pages', function (Blueprint $table) {
            $table->increments('id');

            $table->string('title', 255);
            $table->longText('url_slug');
            $table->longText('body')->nullable();

            $table->boolean('is_active')->default(1);
            $table->integer('order_id')->default(1);

            $table->string('meta_title', 128)->nullable();
            $table->string('meta_keywords', 255)->nullable();
            $table->string('meta_description', 255)->nullable();

            $table->integer('update_by')->nullable();
            $table->integer('created_by')->nullable();

            $table->timestamps(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_pages');
    }
};