<?php

use App\Database\SafeSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('family_trees')) {
            Schema::create('family_trees', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('owner_user_id')->nullable()->index();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('cover_image')->nullable();
                $table->string('visibility')->default('private');
                $table->string('status')->default('active');
                $table->timestamps();
            });

            return;
        }

        SafeSchema::addMissingColumns('family_trees', [
            'owner_user_id' => fn (Blueprint $table) => $table->unsignedBigInteger('owner_user_id')->nullable(),
            'name' => fn (Blueprint $table) => $table->string('name')->nullable(),
            'description' => fn (Blueprint $table) => $table->text('description')->nullable(),
            'cover_image' => fn (Blueprint $table) => $table->string('cover_image')->nullable(),
            'visibility' => fn (Blueprint $table) => $table->string('visibility')->default('private'),
            'status' => fn (Blueprint $table) => $table->string('status')->default('active'),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
        ]);
    }

    public function down(): void
    {
        // Forward-only. An existing family_trees table must survive rollback.
    }
};
