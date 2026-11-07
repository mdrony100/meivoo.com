<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_returns_a_successful_response(): void
    {
        Setting::create([
            'site_name' => 'Meivoo',
            'seo_meta_title' => 'Meivoo',
            'seo_meta_description' => 'Family portal',
            'seo_keywords' => 'family',
        ]);

        $response = $this->get('/');

        $response->assertOk();
    }
}
