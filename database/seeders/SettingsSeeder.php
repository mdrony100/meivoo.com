<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        Setting::updateOrCreate(
            ['id' => 1],
            [
                'site_name' => 'Meivoo',
                'email' => 'admin@meivoo.com',
                'support_email' => 'support@meivoo.com',
                'phone_no' => '+8801000000000',
                'maintenance_mode' => 0,
                'seo_meta_title' => 'Meivoo Admin',
                'seo_meta_description' => 'Admin panel bootstrap data.',
                'seo_keywords' => 'admin,meivoo,laravel',
            ]
        );
    }
}
