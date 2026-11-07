<?php

namespace Database\Seeders;

use App\Models\CustomPage;
use Illuminate\Database\Seeder;

class DefaultPagesSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'url_slug' => 'privacy-policy',
                'title' => 'Privacy Policy',
                'meta_title' => 'Privacy Policy',
                'meta_description' => 'How Meivoo collects, uses and protects your family data.',
                'meta_keywords' => 'privacy policy, data protection, meivoo',
                'body' => '<h2>Your family data stays yours</h2>
<p>Meivoo exists to keep family history together. We collect only what you choose to add: names, dates, photos, stories and the messages you send inside your family space.</p>
<h3>What we store</h3>
<ul>
<li>Account details you provide when you register.</li>
<li>Family tree records, memories and media you upload.</li>
<li>Messages exchanged within your family.</li>
</ul>
<h3>Who can see it</h3>
<p>Content is visible to members of your family space. Private messages are visible only to the people in the conversation. We never sell your data or use it for advertising.</p>
<h3>Your controls</h3>
<p>You can edit or delete your records at any time. Deleting your account removes your profile and the content you created, apart from records other members rely on for the tree.</p>
<h3>Contact</h3>
<p>Questions about your data? Reach us through the contact page and we will respond within a reasonable time.</p>',
            ],
            [
                'url_slug' => 'terms-conditions',
                'title' => 'Terms & Conditions',
                'meta_title' => 'Terms & Conditions',
                'meta_description' => 'The terms that apply when you use Meivoo to preserve your family story.',
                'meta_keywords' => 'terms, conditions, meivoo',
                'body' => '<h2>Using Meivoo</h2>
<p>By creating an account you agree to these terms. Meivoo is a private space for families to record their story, and it depends on members treating each other with respect.</p>
<h3>Your account</h3>
<p>Keep your credentials secure and provide accurate information. You are responsible for activity under your account.</p>
<h3>Content you add</h3>
<p>You keep ownership of the photos, stories and records you upload. By adding them you allow us to store and display them to your family space. Do not upload content you have no right to share.</p>
<h3>Acceptable use</h3>
<ul>
<li>No harassment, hate speech or impersonation.</li>
<li>No attempts to access another family\'s records.</li>
<li>No automated scraping or resale of the service.</li>
</ul>
<h3>Availability</h3>
<p>We work to keep the service available but cannot guarantee uninterrupted access. Features may change as the product develops.</p>
<h3>Ending your use</h3>
<p>You may close your account at any time. We may suspend accounts that break these terms.</p>',
            ],
        ];

        foreach ($pages as $page) {
            CustomPage::updateOrCreate(['url_slug' => $page['url_slug']], $page + ['is_active' => 1]);
        }
    }
}
