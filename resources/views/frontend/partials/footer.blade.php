

<footer class="pt-5 pb-4">
    <div class="container">
        <div class="row g-4">

            <!-- Brand -->
            <div class="col-lg-5">
                @if(!empty($settings->site_logo))
                    <a class="navbar-brand d-flex justify-content-center justify-content-sm-start align-items-center gap-2 text-decoration-none mb-3" href="{{ route('frontend.home') }}">
                            <img src="{{ asset($settings->site_logo) }}"
                                alt="Logo"
                                style="height:60px; width:auto;">
                        {{-- <div class="navbar-brand-icon">
                            <i class="fa-solid fa-tree"></i>
                        </div>
                        <span class="brand-text">Family Legacy</span> --}}
                    </a>
                @endif
                @if ($settings->footer_text)
                    <p class="footer-desc">
                        {{ $settings->footer_text }}
                    </p>
                @endif
                <div class="footer-social d-flex gap-2 mt-3">
                    @if ($settings->twitter_url)
                        <a href="{{ $settings->twitter_url ?? '#!' }}" target="_blank" title="X (Twitter)"><i class="fa-brands fa-x-twitter"
                                style="font-size:13px;"></i></a>
                    @endif
                    @if ($settings->linkedin_url)
                        <a href="{{$settings->linkedin_url ?? '#!' }}" target="_blank" title="LinkedIn"><i class="fa-brands fa-linkedin-in"
                                style="font-size:13px;"></i></a>
                    @endif
                    @if ($settings->facebook_url)
                        <a href="{{$settings->facebook_url ?? '#!' }}" target="_blank" title="Facebook"><i class="fa-brands fa-facebook-f"
                                style="font-size:13px;"></i></a>
                    @endif
                    @if ($settings->instagram_url)
                        <a href="{{$settings->instagram_url ?? '#!' }}" target="_blank" title="Instagram"><i class="fa-brands fa-instagram"
                                style="font-size:13px;"></i></a>
                    @endif
                </div>
            </div>

            <!-- Support -->
            <div class="col-sm-6 col-lg-2">
                <p class="footer-heading">Support</p>
                <a href="{{route('frontend.contact')}}" class="footer-link">Contact Us</a>
                <a href="{{route('frontend.privacy-policy')}}" class="footer-link">Privacy Policy</a>
                <a href="{{route('frontend.terms')}}" class="footer-link">Terms of Service</a>
            </div>

            <!-- Newsletter -->
            <div class="col-sm-6 col-lg-5">
                <p class="footer-heading">Newsletter</p>
                <p style="font-size:15px;color:var(--text-muted);line-height:1.7;margin-bottom:14px;">
                    Get weekly tips on preserving memories, family history ideas, and product updates.
                </p>
                <form action="{{ route('newsletter.subscribe') }}" method="POST">
                    @csrf

                    <input type="email" name="email" class="newsletter-input"
                        placeholder="Your email address" required>

                    <button type="submit" class="btn-newsletter">
                        Subscribe to Newsletter
                    </button>
                </form>
                {{-- <input type="email" class="newsletter-input" placeholder="Your email address">
                <button class="btn-newsletter">Subscribe to Newsletter</button> --}}
            </div>

        </div>

        <div class="footer-bottom text-center gap-2">
            <p class="mb-0">
                &copy; {{ date('Y') }} {{ $settings->site_name }}. All rights reserved.
            </p>
        </div>
    </div>
</footer>
