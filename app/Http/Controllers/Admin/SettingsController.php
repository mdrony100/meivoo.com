<?php

namespace App\Http\Controllers\Admin;

use DateTimeZone;
use App\Models\Setting;
use App\Models\Currency;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\PromptLog;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $settings;
    public $user;

    public function __construct(Setting $settings)
    {
        $this->settings     = $settings;
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    // Setting
    public function index()
    {
        $data['title'] = 'Settings';
        $data['settings'] = Setting::first();
        return view('admin.settings', $data);
    }

    // Update Setting
    public function store(Request $request)
    {
        DB::beginTransaction();

        // dd($request->all());
        try {

            $setting = Setting::first();

            if (!$setting) {
                $setting = new Setting();
            }


            // $promptFields = [
            //     'azure_openai_prompt',
            //     'azure_openai_sql_prompt',
            //     'azure_openai_conversation_prompt',
            //     'azure_openai_summarization_prompt',
            // ];

            // foreach ($promptFields as $field) {
            //     $old = $setting->{$field} ?? null;
            //     $new = $request->input($field);
            //     if ($new !== null && $old !== null && $old !== $new) {
            //         PromptLog::create([
            //             'setting_id' => $setting->id ?? null,
            //             'prompt_key' => $field,
            //             'old_value'  => $old,
            //             'changed_by' => $this->user->id ?? null,
            //         ]);
            //     }
            // }

            if ($request->hasFile('site_logo')) {
                $file = $request->file('site_logo');
                $filename = uniqid().".".$file->getClientOriginalExtension();
                $file->move(public_path('uploads/logo'), $filename);
                $setting->site_logo = 'uploads/logo/'.$filename;
            }
            if ($request->hasFile('seo_image')) {
                $file = $request->file('seo_image');
                $filename = uniqid().".".$file->getClientOriginalExtension();
                $file->move(public_path('uploads/seo'), $filename);
                $setting->seo_image = 'uploads/seo/'.$filename;
            }

            if ($request->hasFile('favicon')) {
                $file = $request->file('favicon');
                $filename = uniqid().".".$file->getClientOriginalExtension();
                $file->move(public_path('uploads/favicon'), $filename);
                $setting->favicon = 'uploads/favicon/'.$filename;
            }

            $setting->site_name              = $request->site_name;
            $setting->image_process_api      = $request->image_process_api;
            // $setting->primary_color          = $request->primary_color;
            // $setting->secondary_color        = $request->secondary_color;
            // $setting->smtp_host             = $request->smtp_host;
            // $setting->smtp_port             = $request->smtp_port;
            // $setting->smtp_username         = $request->smtp_username;
            // $setting->smtp_address          = $request->smtp_address;
            // $setting->azure_endpoint         = $request->azure_endpoint;
            // $setting->azure_key              = $request->azure_key;
            // $setting->maintenance_mode       = $request->maintenance_mode ?? 0;
            // $setting->stripe_enable          = $request->stripe_enable ?? 1;
            // if ($request->has('stripe_publishable_key')) {
            //     $setting->stripe_publishable_key = $request->stripe_publishable_key;
            // }
            // if ($request->filled('stripe_secret')) {
            //     $setting->stripe_secret = $request->stripe_secret;
            // }
            // if ($request->filled('stripe_webhook_secret')) {
            //     $setting->stripe_webhook_secret = $request->stripe_webhook_secret;
            // }
            // $setting->business_hours         = $request->business_hours;
            // $setting->email                  = $request->email;
            $setting->support_email          = $request->support_email;
            // $setting->phone_no               = $request->phone_no;
            // $setting->office_address         = $request->office_address;
            // $setting->seo_meta_title         = $request->seo_meta_title;
            $setting->footer_text            = $request->footer_text;
            $setting->seo_meta_description   = $request->seo_meta_desc;
            $setting->seo_keywords           = $request->seo_keywords;
            $setting->twitter_url            = $request->twitter_url;
            $setting->linkedin_url           = $request->linkedin_url;
            $setting->facebook_url           = $request->facebook_url;
            $setting->instagram_url          = $request->instagram_url;
            $setting->ios_app_url            = $request->ios_app_url;
            $setting->android_app_url        = $request->android_app_url;
            // Microsoft settings
            // $setting->ms_app_name = $request->ms_app_name;
            // $setting->ms_client_id = $request->ms_client_id;
            // if ($request->filled('ms_client_secret')) {
            //     $setting->ms_client_secret = $request->ms_client_secret; // encrypted via model cast
            // }
            // $setting->ms_tenant_id = $request->ms_tenant_id;
            // $setting->ms_redirect_uri = $request->ms_redirect_uri;

            // Google settings
            // $setting->google_app_name = $request->google_app_name;
            // $setting->google_client_id = $request->google_client_id;
            // if ($request->filled('google_client_secret')) {
            //     $setting->google_client_secret = $request->google_client_secret; // encrypted via model cast
            // }
            // $setting->google_redirect = $request->google_redirect;

            // // Azure OpenAI
            // $setting->azure_openai_endpoint             = $request->azure_openai_endpoint;
            // $setting->azure_openai_key                  = $request->azure_openai_key;
            // $setting->azure_openai_deployment           = $request->azure_openai_deployment;
            // $setting->azure_openai_model                = $request->azure_openai_model;
            // $setting->azure_openai_prompt               = $request->azure_openai_prompt;
            // $setting->azure_openai_sql_prompt           = $request->azure_openai_sql_prompt;
            // $setting->azure_openai_conversation_prompt  = $request->azure_openai_conversation_prompt;
            // $setting->azure_openai_summarization_prompt = $request->azure_openai_summarization_prompt;

            // AI Session Expiry Configuration
            // $aiSessionExpiryDays = (int) $request->input('ai_session_expiry_days', 30);
            // $setting->ai_session_expiry_days = max(1, min(365, $aiSessionExpiryDays));

            $setting->save();

            // DB::table('config')->where('config_key', 'stripe_publishable_key')
            //     ->update(['config_value' => $request->stripe_publishable_key]);

            // DB::table('config')->where('config_key', 'stripe_secret')
            //     ->update(['config_value' => $request->stripe_secret]);

            DB::commit();

            return redirect()->back()->with('success', 'Settings updated successfully!');

        } catch (\Exception $e) {
            // dd($e);
            Log::error('SettingsController@store error: '.$e->getMessage(), ['exception' => $e]);
            DB::rollBack();

            return redirect()->back()
                ->with(['error', 'Something went wrong. Please try again']);
        }
    }

}
