<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\Seo;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['name' => 'John Doe', 'email' => 'user1@example.com', 'password' => Hash::make('password'), 'status' => 1, 'is_trial_plan' => 0, 'last_login_at' => now()->subDay()],
            ['name' => 'Catherine Smith', 'email' => 'user2@example.com', 'password' => Hash::make('password'), 'status' => 1, 'is_trial_plan' => 1, 'last_login_at' => now()->subDays(2)],
            ['name' => 'Tom Brown', 'email' => 'user3@example.com', 'password' => Hash::make('password'), 'status' => 0, 'is_trial_plan' => 0, 'last_login_at' => null],
        ] as $user) {
            User::firstOrCreate(['email' => $user['email']], $user);
        }

        foreach ([
            ['title' => 'What is Meivoo?', 'body' => 'Meivoo is a sample FAQ entry for the admin panel.', 'status' => 1, 'order_id' => 1],
            ['title' => 'How do I start?', 'body' => 'Use the admin dashboard to manage content and settings.', 'status' => 1, 'order_id' => 2],
        ] as $faq) {
            Faq::firstOrCreate(['title' => $faq['title']], $faq);
        }
        
        foreach ([
            ['page_slug' => 'home', 'title' => 'Home', 'description' => 'Homepage SEO description.', 'keywords' => 'home,meivoo', 'image' => null],
            ['page_slug' => 'login', 'title' => 'Login', 'description' => 'Sign in to your private family portal.', 'keywords' => 'login,family portal,meivoo', 'image' => null],
            ['page_slug' => 'signup', 'title' => 'Sign Up', 'description' => 'Create your private family portal and join your relatives.', 'keywords' => 'signup,register,family portal', 'image' => null],
            ['page_slug' => 'forgot-password', 'title' => 'Forgot Password', 'description' => 'Recover access to your family portal account.', 'keywords' => 'forgot password,reset password,meivoo', 'image' => null],
            ['page_slug' => 'dashboard', 'title' => 'Dashboard', 'description' => 'View your family dashboard and recent activity.', 'keywords' => 'dashboard,family activity,meivoo', 'image' => null],
            ['page_slug' => 'family-tree', 'title' => 'Family Tree', 'description' => 'Explore and manage your family tree.', 'keywords' => 'family tree,relatives,ancestry', 'image' => null],
            ['page_slug' => 'feed', 'title' => 'Family Feed', 'description' => 'See updates, memories, and posts from your family.', 'keywords' => 'family feed,memories,posts', 'image' => null],
            ['page_slug' => 'members', 'title' => 'Members', 'description' => 'Browse all family members in your private portal.', 'keywords' => 'members,family list,portal', 'image' => null],
            ['page_slug' => 'member-full-profile', 'title' => 'Member Profile', 'description' => 'Read a detailed family member profile.', 'keywords' => 'profile,family member,details', 'image' => null],
            ['page_slug' => 'messages', 'title' => 'Messages', 'description' => 'Send and receive private family messages.', 'keywords' => 'messages,chat,family communication', 'image' => null],
            ['page_slug' => 'notifications', 'title' => 'Notifications', 'description' => 'Review alerts and updates from your family.', 'keywords' => 'notifications,alerts,updates', 'image' => null],
            ['page_slug' => 'settings', 'title' => 'Profile Settings', 'description' => 'Update your profile and portal settings.', 'keywords' => 'settings,profile,account', 'image' => null],
            ['page_slug' => 'contact', 'title' => 'Contact', 'description' => 'Contact page SEO description.', 'keywords' => 'contact,meivoo', 'image' => null],
        ] as $seo) {
            Seo::firstOrCreate(['page_slug' => $seo['page_slug']], $seo);
        }

    }
}
