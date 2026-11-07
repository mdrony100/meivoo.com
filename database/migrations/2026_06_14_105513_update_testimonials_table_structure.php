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
        Schema::table('testimonials', function (Blueprint $table) {
            // Drop old columns (your current structure)
            $table->dropColumn([
                'designation',
                'company_name',
                'order_id',
                'details',
                'rates',
            ]);

            // Add new columns
            $table->string('subtitle')->nullable()->after('name');
            $table->text('message')->after('subtitle');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            // Rollback (restore old structure)
            $table->string('designation')->nullable();
            $table->string('company_name')->nullable();
            $table->unsignedInteger('order_id')->default(0);
            $table->longText('details')->nullable();
            $table->unsignedTinyInteger('rates')->default(5);

            // Remove new columns
            $table->dropColumn(['subtitle', 'message']);
        });
    }
};
