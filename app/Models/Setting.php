<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static orderBy(string $string, string $string1)
 */
class Setting extends Model
{
    protected $table = 'settings';
    protected $fillable = [
        'google_key',
        'google_analytics_id',
        'site_name',
        'site_logo',
        'admin_logo',
        'favicon',
        'seo_meta_title',
        'seo_meta_description',
        'seo_keywords',
        'seo_image',
        'application_type',
        'app_mode',
        'facebook_client_id',
        'facebook_client_secret',
        'facebook_callback_url',
        'google_client_id',
        'google_client_secret',
        'google_callback_url',
        'copyright_text',
        'office_address',
        'facebook_url',
        'youtube_url',
        'twitter_url',
        'linkedin_url',
        'telegram_url',
        'whatsapp_number',
        'instagram_url',
        'ios_app_url',
        'android_app_url',
        'email',
        'phone_no',
        'support_email',
        'map_link',
        'status',
        'footer_text',
        'email_verification',
        'google_recaptcha',
        'recaptcha_site_key',
        'recaptcha_site_secret',
        'header_script',
        'footer_script',
        'currency_symbol',
        'currency_code',
        'google_redirect',
        'image_process_api',
        'maintenance_mode',
        'created_at',
        'updated_at',
    ];

}
