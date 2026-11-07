<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command?->warn('DatabaseSeeder does not run in production. Existing rows were left untouched.');

            return;
        }

        $this->call([
            DefaultPagesSeeder::class,
            RichDemoSeeder::class,
            AdminBootstrapSeeder::class,
            SettingsSeeder::class,
            DemoContentSeeder::class,
        ]);
    }
}
