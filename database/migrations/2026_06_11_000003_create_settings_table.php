<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('google_key')->nullable();
            $table->string('google_analytics_id')->nullable();
            $table->string('site_name')->nullable();
            $table->string('site_logo')->nullable();
            $table->string('admin_logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('seo_meta_title')->nullable();
            $table->text('seo_meta_description')->nullable();
            $table->text('seo_keywords')->nullable();
            $table->string('seo_image')->nullable();
            $table->string('application_type')->nullable();
            $table->string('app_mode')->nullable();
            $table->string('facebook_client_id')->nullable();
            $table->string('facebook_client_secret')->nullable();
            $table->string('facebook_callback_url')->nullable();
            $table->string('google_client_id')->nullable();
            $table->text('google_client_secret')->nullable();
            $table->string('google_callback_url')->nullable();
            $table->string('google_redirect')->nullable();
            $table->text('copyright_text')->nullable();
            $table->text('office_address')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('youtube_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('telegram_url')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('ios_app_url')->nullable();
            $table->string('android_app_url')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_no')->nullable();
            $table->string('support_email')->nullable();
            $table->string('map_link')->nullable();
            $table->boolean('status')->default(true);
            $table->string('footer_text')->nullable();
            $table->boolean('email_verification')->default(false);
            $table->boolean('google_recaptcha')->default(false);
            $table->string('recaptcha_site_key')->nullable();
            $table->text('recaptcha_site_secret')->nullable();
            $table->text('header_script')->nullable();
            $table->text('footer_script')->nullable();
            $table->string('currency_symbol')->nullable();
            $table->string('currency_code', 10)->nullable();
            $table->boolean('maintenance_mode')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
