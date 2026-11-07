@extends('admin.layouts.app')
@section('settings', 'active')
@section('content')
    @php
        $setting = $settings ?? null;
    @endphp


    <div class="col-lg-12">
        <form action="{{ route('admin.settings.store') }}" method="post" enctype="multipart/form-data" id="settingUpdate">
            @csrf
            <div class="">
                <div class="">
                    <div class="row">
                        <div class="col-12">
                            <div class="card content-card mb-3">
                                <div class="card-header">
                                    <h2 class="card-title mb-0">Site Settings</h2>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-6 col-xl-4">
                                            <img src="{{ getLogo($settings->site_logo) }}" height="50px" />
                                            <div class="mb-3">
                                                <label class="form-label">Site Logo
                                                    <br><small class="text-info fw-bold"><strong>(Recommended size
                                                            180x60px)</strong></small>
                                                </label>
                                                <input type="file" class="form-control" name="site_logo"
                                                    placeholder="Site Logo..." accept=".png,.jpg,.jpeg,.gif,.svg" />
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-xl-4">

                                            <img src="{{ getIcon($settings->seo_image) }}" height="50px" />

                                            <div class="mb-3">
                                                <label class="form-label">SEO Image
                                                    <br><small class="text-info fw-bold"><strong>(Recommended size
                                                            728x680px)</strong></small>
                                                </label>
                                                <input type="file" class="form-control" name="seo_image"
                                                    placeholder="SEO Image..." accept=".png,.jpg,.jpeg,.gif,.svg" />
                                            </div>
                                        </div>

                                        <div class="col-lg-6 col-xl-4">
                                            @if ($settings->favicon)
                                                <img src="{{ getIcon($settings->favicon) }}" height="50px" />
                                            @endif
                                            <div class="mb-3">
                                                <label class="form-label"> Favicon
                                                    <br><small class="text-info fw-bold"><strong>(Recommended size
                                                            200x200px)</strong></small>
                                                </label>
                                                <input type="file" class="form-control" name="favicon"
                                                    placeholder=" Favicon..." accept=".png,.jpg,.jpeg,.gif,.svg" />
                                            </div>
                                        </div>

                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label class="form-label">App Name</label>
                                                <input type="text" class="form-control" name="app_name"
                                                    value="{{ config('app.name') }}" placeholder="App Name..." readonly>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label class="form-label required">Site Name</label>
                                                <input type="text" class="form-control" name="site_name"
                                                    value="{{ $settings->site_name }}" placeholder="Site Name..." required>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label class="form-label">Support Email</label>
                                                <input type="support_email" name="support_email" class="form-control"
                                                    value="{{ $settings->support_email }}">
                                                @error('support_email')
                                                    <span class="help-block text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label class="form-label required">SEO Meta Description</label>
                                                <textarea class="form-control" name="seo_meta_desc" rows="3" placeholder="SEO Meta Description"
                                                    style="height: 120px !important;" required>{{ $settings->seo_meta_description }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label class="form-label">SEO Keywords</label>
                                                <textarea class="form-control required" name="seo_keywords" rows="3" placeholder="SEO Keywords"
                                                    style="height: 120px !important;" required>{{ $settings->seo_keywords }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label class="form-label">Footer Text</label>
                                                <textarea class="form-control required" name="footer_text" rows="3" placeholder="SEO Keywords"
                                                    style="height: 120px !important;" required>{{ $settings->footer_text }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- General Settings --}}
                        {{-- <div class="col-lg-12">
                            <div class="card content-card mb-3">
                                <div class="card-header">
                                    <h2 class="card-title mb-0">General Settings</h2>
                                </div>
                                <div class="card-body">

                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label">Email</label>
                                                <input type="email" name="email" class="form-control"
                                                    value="{{ $settings->email }}" required>
                                                @error('email')
                                                    <span class="help-block text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label">Support Email</label>
                                                <input type="support_email" name="support_email" class="form-control"
                                                    value="{{ $settings->support_email }}">
                                                @error('support_email')
                                                    <span class="help-block text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('messages.common.phone') }}</label>
                                                <input type="phone_no" name="phone_no" required class="form-control"
                                                    value="{{ $settings->phone_no }}">
                                                @error('phone_no')
                                                    <span class="help-block text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label">Email Verified Enable</label>
                                                <select name="email_verified" class="form-control">
                                                    <option value="1"
                                                        {{ isset($settings->email_verified) && $settings->email_verified == 1 ? 'selected' : '' }}>
                                                        Yes</option>
                                                    <option value="0"
                                                        {{ isset($settings->email_verified) && $settings->email_verified == 0 ? 'selected' : '' }}>
                                                        No</option>
                                                </select>
                                            </div>
                                        </div>


                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label">Email Enable</label>
                                                <select name="email_enabled" class="form-control">
                                                    <option value="1"
                                                        {{ isset($settings->email_enabled) && $settings->email_enabled == 1 ? 'selected' : '' }}>
                                                        Yes</option>
                                                    <option value="0"
                                                        {{ isset($settings->email_enabled) && $settings->email_enabled == 0 ? 'selected' : '' }}>
                                                        No</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="mb-3">
                                                <label class="form-label">Office Address</label>
                                                <textarea class="form-control" name="office_address" rows="3" placeholder="Office Address"
                                                    style="height: 120px !important;" required>{{ $settings->office_address }}</textarea>

                                                @error('office_address')
                                                    <span class="help-block text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div> --}}
                        {{-- Social --}}
                        <div class="col-lg-12">
                            <div class="card content-card mb-3">
                                <div class="card-header">
                                    <h2 class="card-title mb-0">Social URL</h2>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('Twitter Url') }}</label>
                                                <input type="url" class="form-control" name="twitter_url"
                                                    value="{{ $settings->twitter_url }}"
                                                    placeholder="{{ __('Twitter Url') }}...">
                                                @error('twitter_url')
                                                    <span class="help-block text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('Linkedin url') }}</label>
                                                <input type="url" class="form-control" name="linkedin_url"
                                                    value="{{ $settings->linkedin_url }}"
                                                    placeholder="{{ __('Linkedin url') }}...">
                                                @error('linkedin_url')
                                                    <span class="help-block text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('Facebook URL') }}</label>
                                                <input type="url" class="form-control" name="facebook_url"
                                                    value="{{ $settings->facebook_url }}"
                                                    placeholder="{{ __('Facebook URL') }}...">
                                                @error('facebook_url')
                                                    <span class="help-block text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('Instagram Url') }}</label>
                                                <input type="url" class="form-control" name="instagram_url"
                                                    value="{{ $settings->instagram_url }}"
                                                    placeholder="{{ __('Twitter Url') }}...">
                                                @error('instagram_url')
                                                    <span class="help-block text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- API Credentials Settings --}}
                        <div class="col-lg-12">
                            <div class="card content-card mb-3">
                                <div class="card-header">
                                    <h2 class="card-title mb-0">API Credentials</h2>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="mb-3">
                                                <label class="form-label">Image Enhancement Token
                                                    <small class="text-muted ms-1">( Get token from <a
                                                            href="https://replicate.com/account/api-tokens"
                                                            target="_blank"
                                                            class="text-primary fw-bold text-decoration-underline">Replicate
                                                            API Tokens</a> )</small>
                                                </label>
                                                <input type="password" class="form-control" name="image_process_api"
                                                    value="{{ old('image_process_api', $settings->image_process_api ?? '') }}"
                                                    placeholder="r8_********************************">
                                                <small class="form-text text-muted">
                                                    <i class="ti ti-info-circle"></i> <strong>Note:</strong> Only API token
                                                    from Replicate.com will work for photo enhancement tools.
                                                </small>
                                                @error('image_process_api')
                                                    <span
                                                        class="help-block text-danger d-block mt-1">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Apps URL --}}
                        <div class="col-lg-12">
                            <div class="card content-card mb-3">
                                <div class="card-header">
                                    <h2 class="card-title mb-0">Apps URL</h2>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label">Apple App Link</label>
                                                <input type="url" class="form-control" name="ios_app_url"
                                                    value="{{ old('ios_app_url', $settings->ios_app_url ?? '') }}"
                                                    placeholder="https://apps.apple.com/...">
                                                @error('ios_app_url')
                                                    <span class="help-block text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label">Android App Link</label>
                                                <input type="url" class="form-control" name="android_app_url"
                                                    value="{{ old('android_app_url', $settings->android_app_url ?? '') }}"
                                                    placeholder="https://play.google.com/...">
                                                @error('android_app_url')
                                                    <span class="help-block text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary btn-icon" id="updateButton"> Save Settings</button>
                </div>
            </div>
        </form>
    </div>
    {{-- <form method="POST" action="{{ route('admin.settings.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="row g-3">
        <div class="col-xl-12">
            <div class="card content-card mb-3">
                <div class="card-header"><h2 class="card-title mb-0">General Settings</h2></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="site_name">Site Name</label>
                            <input type="text" name="site_name" id="site_name" value="{{ old('site_name', $setting->site_name ?? '') }}" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="primary_color">Primary Color</label>
                            <input type="color" name="primary_color" id="primary_color" value="{{ old('primary_color', $setting->primary_color ?? '#2563eb') }}" class="form-control form-control-color">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="secondary_color">Secondary Color</label>
                            <input type="color" name="secondary_color" id="secondary_color" value="{{ old('secondary_color', $setting->secondary_color ?? '#0f172a') }}" class="form-control form-control-color">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="site_logo">Site Logo</label>
                            <input type="file" name="site_logo" id="site_logo" class="form-control" accept="image/*">
                            @if (!empty($setting->site_logo))
                                <img src="{{ asset($setting->site_logo) }}" alt="Site logo" class="mt-2 rounded border" height="42">
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="favicon">Favicon</label>
                            <input type="file" name="favicon" id="favicon" class="form-control" accept="image/*">
                            @if (!empty($setting->favicon))
                                <img src="{{ asset($setting->favicon) }}" alt="Favicon" class="mt-2 rounded border" height="32">
                            @endif
                        </div>
                        <div class="col-md-6">
                            <input type="hidden" name="maintenance_mode" value="0">
                            <div class="form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" name="maintenance_mode" id="maintenance_mode" value="1" {{ old('maintenance_mode', $setting->maintenance_mode ?? 0) ? 'checked' : '' }}>
                                <label class="form-check-label" for="maintenance_mode">Maintenance Mode</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card content-card mb-3">
                <div class="card-header"><h2 class="card-title mb-0">Contact & SEO</h2></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $setting->email ?? '') }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="support_email">Support Email</label>
                            <input type="email" name="support_email" id="support_email" value="{{ old('support_email', $setting->support_email ?? '') }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="phone_no">Phone</label>
                            <input type="text" name="phone_no" id="phone_no" value="{{ old('phone_no', $setting->phone_no ?? '') }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="business_hours">Business Hours</label>
                            <input type="text" name="business_hours" id="business_hours" value="{{ old('business_hours', $setting->business_hours ?? '') }}" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="office_address">Office Address</label>
                            <textarea name="office_address" id="office_address" rows="2" class="form-control">{{ old('office_address', $setting->office_address ?? '') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="seo_meta_title">SEO Meta Title</label>
                            <input type="text" name="seo_meta_title" id="seo_meta_title" value="{{ old('seo_meta_title', $setting->seo_meta_title ?? '') }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="seo_keywords">SEO Keywords</label>
                            <input type="text" name="seo_keywords" id="seo_keywords" value="{{ old('seo_keywords', $setting->seo_keywords ?? '') }}" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="seo_meta_description">SEO Meta Description</label>
                            <textarea name="seo_meta_description" id="seo_meta_description" rows="3" class="form-control">{{ old('seo_meta_description', $setting->seo_meta_description ?? '') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="twitter_url">Twitter URL</label>
                            <input type="url" name="twitter_url" id="twitter_url" value="{{ old('twitter_url', $setting->twitter_url ?? '') }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="linkedin_url">LinkedIn URL</label>
                            <input type="url" name="linkedin_url" id="linkedin_url" value="{{ old('linkedin_url', $setting->linkedin_url ?? '') }}" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="position-sticky bottom-0 py-3 bg-body-tertiary text-end">
        <button type="submit" class="btn btn-primary btn-icon"><i class="bi bi-check2"></i>Save Settings</button>
    </div>
</form> --}}

    {{-- paypal setting --}}
    {{-- <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">{{__('messages.settings.paypal_settings')}}</h3>
                                </div>
                                <div class="card-body">

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="mb-3">
                                                <label
                                                    class="form-label required">{{ __('messages.settings.mode') }}</label>
                                                <select type="text" class="form-select form-control"
                                                    placeholder="Select a payment mode"
                                                    id="select-tags-advanced" name="paypal_mode" required>
                                                    <option value="sandbox"
                                                        {{ $config[3]->config_value == 'sandbox' ? 'selected' : '' }}>
                                                        {{ __('Sandbox') }}</option>
                                                    <option value="live"
                                                        {{ $config[3]->config_value == 'live' ? 'selected' : '' }}>
                                                        {{ __('Live') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="mb-3">
                                                <label
                                                    class="form-label required">{{ __('messages.settings.client_key') }}</label>
                                                <input type="text" class="form-control"
                                                    name="paypal_client_key"
                                                    value="{{ $config[4]->config_value }}"
                                                    placeholder="{{ __('Client Key') }}..." required>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="mb-3">
                                                <label class="form-label"
                                                    required>{{ __('messages.settings.secret') }}</label>
                                                <input type="text" class="form-control"
                                                    name="paypal_secret"
                                                    value="{{ $config[5]->config_value }}"
                                                    placeholder="{{ __('Secret') }}..." required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> --}}

    {{-- strip setting --}}
    {{-- <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">{{__('messages.settings.stripe_settings')}}</h3>
                                </div>
                                <div class="card-body">

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="mb-3">
                                                <label
                                                    class="form-label required">{{ __('messages.settings.mode') }}</label>
                                                <select type="text" class="form-select form-control"
                                                    placeholder="Select a payment mode"
                                                    id="select-tags-advanced" name="paypal_mode" required>
                                                    <option value="sandbox"
                                                        {{ $config[3]->config_value == 'sandbox' ? 'selected' : '' }}>
                                                        {{ __('Sandbox') }}</option>
                                                    <option value="live"
                                                        {{ $config[3]->config_value == 'live' ? 'selected' : '' }}>
                                                        {{ __('Live') }}</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-12">
                                            <div class="mb-3">
                                                <label
                                                    class="form-label required">{{ __('messages.settings.publishable_key') }}</label>
                                                <input type="text" class="form-control"
                                                    name="stripe_publishable_key"
                                                    value="{{ $config[9]->config_value }}"
                                                    placeholder="{{ __('Publishable Key') }}..." required>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="mb-3">
                                                <label
                                                    class="form-label required">{{ __('messages.settings.secret') }}</label>
                                                <input type="text" class="form-control"
                                                    name="stripe_secret"
                                                    value="{{ $config[10]->config_value }}"
                                                    placeholder="{{ __('Secret') }}..." required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
@endsection
