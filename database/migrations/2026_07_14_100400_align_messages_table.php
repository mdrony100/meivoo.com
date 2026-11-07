<?php

use App\Database\AlignMessages;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        AlignMessages::up();
    }

    public function down(): void
    {
        AlignMessages::down();
    }
};
