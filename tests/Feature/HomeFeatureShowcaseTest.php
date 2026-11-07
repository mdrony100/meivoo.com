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
        $response->assertSee('data-explore-viewport', false);
        $response->assertSee('data-explore-toggle', false);
        $response->assertSee('Pause automatic slides', false);
        $response->assertSee(route('user.family-tree'), false);
        $response->assertSee(route('user.members'), false);
        $response->assertSee(route('user.messages'), false);
        $response->assertSee(route('user.events'), false);
        $response->assertSee(route('user.legacy-names'), false);
        $response->assertSee(route('user.ai-photo.index'), false);
        $response->assertSee('assets/images/explore/family-tree.jpg', false);
        $response->assertSee('assets/images/explore/family-members.jpg', false);
        $response->assertSee('assets/images/explore/messages.jpg', false);
        $response->assertSee('assets/images/explore/events.jpg', false);
        $response->assertSee('assets/images/explore/legacy-names.jpg', false);
        $response->assertSee('assets/images/explore/photo-restoration.jpg', false);
        $response->assertSee('loading="lazy"', false);
        $html = $response->getContent();
        $start = (int) strpos($html, 'id="explore"');
        $end = (int) strpos($html, 'id="tree"');
        $explore = substr($html, $start, $end - $start);
        $this->assertStringNotContainsString('family-chat-screen.png', $explore);
        $this->assertStringNotContainsString('ai-photo-restoration-mobile.png', $explore);
        $this->assertStringNotContainsString('family-legacy-mobile-app.png', $explore);
        $response->assertDontSee('Apple Invites', false);
        $response->assertDontSee('FaceTime', false);

        $css = file_get_contents(public_path('assets/mv-site.css'));
        $exploreCss = substr($css, (int) strpos($css, 'homepage feature carousel'));
        $this->assertStringNotContainsString('100vw', $exploreCss);
        $this->assertStringNotContainsString('overflow-x: auto', $exploreCss);

        $script = file_get_contents(public_path('assets/explore.js'));
        $this->assertStringContainsString('prefers-reduced-motion', $script);
        $this->assertStringContainsString('translate3d', $script);
    }
}
