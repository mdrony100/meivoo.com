<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeFeatureShowcaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_keeps_existing_sections_and_adds_real_feature_cards(): void
    {
        Setting::create([
            'site_name' => 'Meivoo',
            'seo_meta_title' => 'Meivoo',
            'seo_meta_description' => 'Family portal',
            'seo_keywords' => 'family',
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Preserve Your', false);
        $response->assertSee('id="tree"', false);
        $response->assertSee('id="features"', false);
        $response->assertSee('id="explore"', false);
        $response->assertSee('Family tree', false);
        $response->assertSee('Photo restoration', false);
        $response->assertSee('role="tablist"', false);
        $response->assertSee(route('user.family-tree'), false);
        $response->assertSee(route('user.members'), false);
        $response->assertSee(route('user.messages'), false);
        $response->assertSee(route('user.events'), false);
        $response->assertSee(route('user.legacy-names'), false);
        $response->assertSee(route('user.ai-photo.index'), false);
        $response->assertSee('assets/images/family-chat-screen.png', false);
        $response->assertSee('assets/images/ai-photo-restoration-mobile.png', false);
        $response->assertDontSee('Apple Invites', false);
        $response->assertDontSee('FaceTime', false);
    }
}
