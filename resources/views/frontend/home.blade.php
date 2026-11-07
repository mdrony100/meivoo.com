@extends('layouts.frontend')
@section('title', 'Preserve Your Family Story Forever')
@section('content')
    <section class="hero-section">
        <div class="hero-blob-1"></div>
        <div class="hero-blob-2"></div>

        <div class="container position-relative" style="z-index:2;">
            <div class="row align-items-center g-3 g-md-4 g-lg-5">

                <!-- Left col -->
                <div class="col-lg-6" data-aos="fade-right" data-aos-duration="700">
                    <div class="badge-trusted">
                        <i class="fa-solid fa-heart"></i>
                        Trusted by 50,000+ families worldwide
                    </div>

                    <h1 class="hero-title font-playfair">
                        Preserve Your <span class="text-gradient">Family Story</span> Forever
                    </h1>

                    <p class="hero-lead mt-3">
                        Build your private family tree, save memories, restore old photos with AI, and connect generations
                        together.
                    </p>

                    <div class="hero-img-wrap d-lg-none">
                        <img src="{{ asset('assets/images/hero-family-gathering.png') }}"
                            alt="Happy family gathering together">
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('register') }}" class="btn-hero-cta">
                            Create Family Portal
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>

                    <div class="d-flex align-items-center justify-content-center justify-content-sm-start gap-3 mt-4">
                        <div class="avatar-group">
                            <div class="av"><img src="https://randomuser.me/api/portraits/women/44.jpg"
                                    alt="Family member avatar 1" loading="lazy"></div>
                            <div class="av"><img src="https://randomuser.me/api/portraits/men/32.jpg"
                                    alt="Family member avatar 2" loading="lazy"></div>
                            <div class="av"><img src="https://randomuser.me/api/portraits/women/68.jpg"
                                    alt="Family member avatar 3" loading="lazy"></div>
                            <div class="av">A</div>
                        </div>
                        <div>
                            <div class="stars">★★★★★</div>
                            <p class="review-text mb-0">4.9/5 from 2,400+ reviews</p>
                        </div>
                    </div>
                </div>

                <!-- Right col – image -->
                <div class="col-lg-6 position-relative" data-aos="fade-left" data-aos-duration="700" data-aos-delay="100">
                    <div class="hero-img-wrap d-none d-lg-inline-block">
                        <img src="{{ asset('assets/images/hero-family-gathering.png') }}"
                            alt="Happy family gathering together">
                    </div>

                    <!-- Floating cards -->
                    <div class="float-card glass-strong fc-members" data-aos="zoom-in" data-aos-delay="400">
                        <div class="float-card-icon" style="background:rgba(124,58,237,0.1);">
                            <i class="fa-solid fa-users" style="color:var(--primary);font-size:18px;"></i>
                        </div>
                        <div>
                            <p class="fc-title">Family Members</p>
                            <p class="fc-sub">4 generations connected</p>
                        </div>
                    </div>

                    <div class="float-card glass-strong fc-photos" data-aos="zoom-in" data-aos-delay="500">
                        <div class="float-card-icon" style="background:rgba(6,182,212,0.1);">
                            <i class="fa-regular fa-image" style="color:var(--accent);font-size:18px;"></i>
                        </div>
                        <div>
                            <p class="fc-title">Photos Restored</p>
                            <p class="fc-sub">2,847 memories saved</p>
                        </div>
                    </div>

                    <div class="float-card glass-strong fc-voice" data-aos="zoom-in" data-aos-delay="600">
                        <div class="float-card-icon" style="background:#fce7f3;">
                            <i class="fa-solid fa-microphone" style="color:#ec4899;font-size:18px;"></i>
                        </div>
                        <div>
                            <p class="fc-title">Voice Memory</p>
                            <p class="fc-sub">Grandma's story • 4:32</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    @include('frontend.partials.feature_showcase')

    <!-- ═══════════════════════════════════════════
                         FAMILY TREE SECTION
                      ═══════════════════════════════════════════ -->
    <section id="tree" class="py-section position-relative">
        <div class="tree-bg-blob"></div>
        <div class="container position-relative" style="z-index:1;">

            <div class="text-center mb-0 mb-md-4 mb-lg-5" data-aos="fade-up">
                <span class="section-label">Interactive Family Tree</span>
                <h2 class="section-title font-playfair mt-2">
                    Your Family, <span class="text-gradient">Beautifully Connected</span>
                </h2>
                <p class="section-lead mt-3 mx-auto" style="max-width:520px;">
                    Visualize generations of family bonds with our elegant, interactive family tree builder.
                </p>
            </div>

            <div class="tree-canvas" data-aos="fade-up" data-aos-delay="100">
                <!-- SVG connectors -->
                <svg xmlns="http://www.w3.org/2000/svg">
                    <line x1="50%" y1="12%" x2="30%" y2="38%" stroke="hsl(263,70%,50%)"
                        stroke-width="2" stroke-opacity="0.25" stroke-dasharray="4 4" />
                    <line x1="50%" y1="12%" x2="70%" y2="38%" stroke="hsl(263,70%,50%)"
                        stroke-width="2" stroke-opacity="0.25" stroke-dasharray="4 4" />
                    <line x1="30%" y1="42%" x2="18%" y2="68%" stroke="hsl(263,70%,50%)"
                        stroke-width="2" stroke-opacity="0.25" stroke-dasharray="4 4" />
                    <line x1="30%" y1="42%" x2="40%" y2="68%" stroke="hsl(263,70%,50%)"
                        stroke-width="2" stroke-opacity="0.25" stroke-dasharray="4 4" />
                    <line x1="70%" y1="42%" x2="62%" y2="68%" stroke="hsl(263,70%,50%)"
                        stroke-width="2" stroke-opacity="0.25" stroke-dasharray="4 4" />
                    <line x1="70%" y1="42%" x2="82%" y2="68%" stroke="hsl(263,70%,50%)"
                        stroke-width="2" stroke-opacity="0.25" stroke-dasharray="4 4" />
                </svg>

                <!-- Nodes -->
                <div class="tree-node" style="left:50%;top:10%;">
                    <div class="tree-avatar bg-gradient-purple">R</div>
                    <div class="tree-label glass-strong">
                        <p class="tl-name">Robert</p>
                        <p class="tl-role">Grandfather</p>
                    </div>
                </div>
                <div class="tree-node" style="left:30%;top:38%;">
                    <div class="tree-avatar bg-gradient-blue"><img src="https://randomuser.me/api/portraits/women/65.jpg"
                            alt="Sarah avatar" loading="lazy"></div>
                    <div class="tree-label glass-strong">
                        <p class="tl-name">Sarah</p>
                        <p class="tl-role">Mother</p>
                    </div>
                </div>
                <div class="tree-node" style="left:70%;top:38%;">
                    <div class="tree-avatar bg-gradient-blue"><img src="https://randomuser.me/api/portraits/men/75.jpg"
                            alt="James avatar" loading="lazy"></div>
                    <div class="tree-label glass-strong">
                        <p class="tl-name">James</p>
                        <p class="tl-role">Father</p>
                    </div>
                </div>
                <div class="tree-node" style="left:18%;top:68%;">
                    <div class="tree-avatar bg-gradient-purple"><img
                            src="https://randomuser.me/api/portraits/women/24.jpg" alt="Emma avatar" loading="lazy">
                    </div>
                    <div class="tree-label glass-strong">
                        <p class="tl-name">Emma</p>
                        <p class="tl-role">Daughter</p>
                    </div>
                </div>
                <div class="tree-node" style="left:40%;top:68%;">
                    <div class="tree-avatar bg-gradient-purple"><img src="https://randomuser.me/api/portraits/men/12.jpg"
                            alt="Liam avatar" loading="lazy"></div>
                    <div class="tree-label glass-strong">
                        <p class="tl-name">Liam</p>
                        <p class="tl-role">Son</p>
                    </div>
                </div>
                <div class="tree-node" style="left:62%;top:68%;">
                    <div class="tree-avatar bg-gradient-purple"><img
                            src="https://randomuser.me/api/portraits/women/47.jpg" alt="Olivia avatar" loading="lazy">
                    </div>
                    <div class="tree-label glass-strong">
                        <p class="tl-name">Olivia</p>
                        <p class="tl-role">Daughter</p>
                    </div>
                </div>
                <div class="tree-node" style="left:82%;top:68%;">
                    <div class="tree-avatar bg-gradient-purple"><img src="https://randomuser.me/api/portraits/men/55.jpg"
                            alt="Noah avatar" loading="lazy"></div>
                    <div class="tree-label glass-strong">
                        <p class="tl-name">Noah</p>
                        <p class="tl-role">Son</p>
                    </div>
                </div>
            </div>

            <p class="tree-note mt-0 mt-md-3" data-aos="fade-up" data-aos-delay="200">
                Drag, zoom, and explore your family connections across generations
            </p>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════
                         MEMORY PRESERVATION SECTION
                      ═══════════════════════════════════════════ -->
    <section class="memory-section py-section">
        <div class="container">
            <div class="container-fluid">
                <div class="row align-items-center g-5">

                    <!-- Image col -->
                    <div class="col-lg-6 order-2 order-lg-1" data-aos="fade-right" data-aos-duration="700">
                        <div class="position-relative">
                            <div class="memory-img-wrap">
                                <img src="{{ asset('assets/images/memory-recording.png') }}"
                                    alt="Grandmother recording voice memories" class="w-100">
                            </div>
                            <div class="recording-card glass-strong d-none d-sm-flex">
                                <div class="rec-icon"><i class="fa-solid fa-microphone"></i></div>
                                <div>
                                    <p style="font-size:.82rem;font-weight:600;margin:0 0 4px;">Recording Memory...</p>
                                    <div class="d-flex align-items-center gap-1">
                                        <div class="wave-bars">
                                            <span></span><span></span><span></span><span></span><span></span>
                                            <span></span><span></span><span></span><span></span><span></span>
                                            <span></span><span></span>
                                        </div>
                                        <span style="font-size:.72rem;color:var(--text-muted);margin-left:6px;">2:47</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Text col -->
                    <div class="col-lg-6 order-1 order-lg-2" data-aos="fade-left" data-aos-duration="700">
                        <span class="section-label">Memory Preservation</span>
                        <h2 class="section-title font-playfair mt-2">
                            Every Memory <span class="text-gradient">Matters</span>
                        </h2>
                        <p class="section-lead mt-3">
                            From grandmother's recipes to baby's first words — preserve every precious moment for future
                            generations.
                        </p>

                        <div class="row g-2 mt-3">
                            <div class="col-sm-6">
                                <div class="memory-feature-item">
                                    <div class="mf-icon" style="background:#fdf2f8;color:#ec4899;"><i
                                            class="fa-solid fa-microphone"></i>
                                    </div>
                                    <div>
                                        <p class="mf-title">Voice Memories</p>
                                        <p class="mf-sub">Grandma's stories preserved forever</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="memory-feature-item">
                                    <div class="mf-icon" style="background:var(--primary-light);color:var(--primary);"><i
                                            class="fa-solid fa-book-open"></i></div>
                                    <div>
                                        <p class="mf-title">Family Stories</p>
                                        <p class="mf-sub">Written tales passed down generations</p>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="col-sm-6">
                                <div class="memory-feature-item">
                                    <div class="mf-icon" style="background:var(--accent-light);color:var(--accent);"><i
                                            class="fa-solid fa-calendar"></i></div>
                                    <div>
                                        <p class="mf-title">Milestones</p>
                                        <p class="mf-sub">Weddings, birthdays, graduations</p>
                                    </div>
                                </div>
                            </div> --}}
                            <div class="col-sm-6">
                                <div class="memory-feature-item">
                                    <div class="mf-icon" style="background:#fffbeb;color:#f59e0b;"><i
                                            class="fa-solid fa-baby"></i></div>
                                    <div>
                                        <p class="mf-title">First Moments</p>
                                        <p class="mf-sub">Baby's first steps, first words</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="memory-feature-item">
                                    <div class="mf-icon" style="background:#f0fdf4;color:#10b981;"><i
                                            class="fa-solid fa-camera"></i>
                                    </div>
                                    <div>
                                        <p class="mf-title">Photo Albums</p>
                                        <p class="mf-sub">Curated family photo collections</p>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="col-sm-6">
                                <div class="memory-feature-item">
                                    <div class="mf-icon" style="background:#fff1f2;color:#f43f5e;"><i
                                            class="fa-solid fa-heart"></i></div>
                                    <div>
                                        <p class="mf-title">Love Letters</p>
                                        <p class="mf-sub">Letters between generations</p>
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    {{-- <!-- ═══════════════════════════════════════════
     AI PHOTO RESTORATION
  ═══════════════════════════════════════════ -->
  <section id="ai-photos" class="py-section">
    <div class="container">
      <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
          <div class="badge-ai">
            <i class="fa-solid fa-sparkles"></i>
            Powered by AI
          </div>
          <h2 class="section-title font-playfair">
            AI Photo <span class="text-gradient">Restoration Magic</span>
          </h2>
          <p class="section-lead mt-3 mx-auto" style="max-width:520px;">
            Transform old, damaged photos into vibrant, restored memories with our cutting-edge AI technology.
          </p>
        </div>

        <div class="row align-items-center g-5">

          <!-- Before/After -->
          <div class="col-lg-5" data-aos="fade-right" data-aos-duration="700">
            <div class="compare-card">
              <div class="compare-frame">
                <div class="loader-container">
                  <div class="loader" aria-hidden="true"></div>
                  <div class="before_after">
                    <img src="{{ asset('assets/images/original-damaged-photo.png') }}" alt="Original damaged photo"
                      onload="imageLoaded(this)">
                    <img src="{{ asset('assets/images/restored-photo.png') }}" alt="Restored photo" onload="imageLoaded(this)">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Features list -->
          <div class="col-lg-7" data-aos="fade-left" data-aos-duration="700">
            <div class="d-flex flex-column gap-3">
              <div class="ai-feature-card">
                <div class="ai-feat-icon"><i class="fa-solid fa-wand-magic-sparkles"></i></div>
                <div>
                  <p class="ai-feat-title">Restore Damaged Photos</p>
                  <p class="ai-feat-sub">Fix tears, scratches, and water damage</p>
                </div>
              </div>
              <div class="ai-feature-card">
                <div class="ai-feat-icon"><i class="fa-solid fa-palette"></i></div>
                <div>
                  <p class="ai-feat-title">Colorize B&amp;W Photos</p>
                  <p class="ai-feat-sub">Bring life to black and white memories</p>
                </div>
              </div>
              <div class="ai-feature-card">
                <div class="ai-feat-icon"><i class="fa-solid fa-eraser"></i></div>
                <div>
                  <p class="ai-feat-title">Remove Blur &amp; Noise</p>
                  <p class="ai-feat-sub">Crystal clear results from old prints</p>
                </div>
              </div>
              <div class="ai-feature-card">
                <div class="ai-feat-icon"><i class="fa-solid fa-user-plus"></i></div>
                <div>
                  <p class="ai-feat-title">Add Missing Members</p>
                  <p class="ai-feat-sub">Combine family members into group photos</p>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </section> --}}

    <!-- ═══════════════════════════════════════════
                         PRIVATE FAMILY NETWORK
                      ═══════════════════════════════════════════ -->
    <section class="social-section py-section">
        <div class="container">

            <div class="text-center mb-5" data-aos="fade-up">
                <span class="section-label">Private Family Network</span>
                <h2 class="section-title font-playfair mt-2">
                    A Social Network <span class="text-gradient">Just for Family</span>
                </h2>
                <p class="section-lead mt-3 mx-auto" style="max-width:520px;">
                    Share updates, photos, and milestones in a completely private space designed exclusively for your
                    family.
                </p>
            </div>

            <!-- Sample post card -->
            <div data-aos="fade-up" data-aos-delay="100">
                <div class="social-post-card glass-strong">
                    <div class="post-header d-flex align-items-center gap-3">
                        <div class="post-avatar"><img src="https://randomuser.me/api/portraits/women/68.jpg"
                                alt="Sarah Johnson avatar" loading="lazy"></div>
                        <div>
                            <p class="post-name">Sarah Johnson</p>
                            <p class="post-meta">2 hours ago · The Johnson Family</p>
                        </div>
                    </div>
                    <div class="post-body">
                        Look who took their first steps today! 🎉 Our little Emma is growing so fast. Grandma was there to
                        capture
                        the whole thing! ❤️
                    </div>
                    <div class="post-media">
                        <span>👶🏻</span>
                        <p>Family Photo</p>
                    </div>
                    <div class="post-actions">
                        <div class="d-flex gap-3">
                            <button class="post-action-btn"><i class="fa-regular fa-thumbs-up"></i> 24</button>
                            <button class="post-action-btn"><i class="fa-regular fa-comment-dots"></i> 8</button>
                        </div>
                        <span class="post-loved">Loved by the whole family</span>
                    </div>
                </div>
            </div>

            <!-- Feature grid -->
            <div class="row g-4 mt-2">
                <div class="col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="0">
                    <div class="feature-icon-card">
                        <div class="fi-icon" style="background:var(--primary-light);color:var(--primary);"><i
                                class="fa-regular fa-comment-dots fs-5"></i></div>
                        <p class="fi-title">Family Chat</p>
                        <p class="fi-sub">Private messaging for your entire family</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="0">
                    <div class="feature-icon-card">
                        <div class="fi-icon" style="background:#fdf2f8;color:#ec4899;"><i
                                class="fa-solid fa-thumbs-up fs-5"></i>
                        </div>
                        <p class="fi-title">React &amp; Comment</p>
                        <p class="fi-sub">Engage with family posts and updates</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="60">
                    <div class="feature-icon-card">
                        <div class="fi-icon" style="background:#f0fdf4;color:#10b981;"><i
                                class="fa-solid fa-globe fs-5"></i></div>
                        <p class="fi-title">Global Connection</p>
                        <p class="fi-sub">Connect family across the world</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="120">
                    <div class="feature-icon-card">
                        <div class="fi-icon" style="background:#f5f5f5;color:#555;"><i class="fa-solid fa-lock fs-5"></i>
                        </div>
                        <p class="fi-title">100% Private</p>
                        <p class="fi-sub">Only your family sees your content</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════
                         HOW IT WORKS
                      ═══════════════════════════════════════════ -->
    <section class="how-section py-section">
        <div class="container">

            <div class="text-center mb-5" data-aos="fade-up">
                <span class="section-label">Simple &amp; Easy</span>
                <h2 class="section-title font-playfair mt-2">
                    How It <span class="text-gradient">Works</span>
                </h2>
                <p class="section-lead mt-3">Get started in minutes — no technical knowledge required.</p>
            </div>

            <div class="row g-4 g-lg-0">
                <div class="col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="0">
                    <div class="step-wrap">
                        <div class="d-none d-lg-block step-connector"></div>
                        <div class="position-relative d-inline-block">
                            <div class="step-icon-box"><i class="fa-solid fa-user-plus"></i></div>
                            <span class="step-number">01</span>
                        </div>
                        <p class="step-title">Create Family Portal</p>
                        <p class="step-sub">Set up your private family space in seconds</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                    <div class="step-wrap">
                        <div class="d-none d-lg-block step-connector"></div>
                        <div class="position-relative d-inline-block">
                            <div class="step-icon-box"><i class="fa-solid fa-users"></i></div>
                            <span class="step-number">02</span>
                        </div>
                        <p class="step-title">Invite Family Members</p>
                        <p class="step-sub">Add relatives from anywhere in the world</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                    <div class="step-wrap">
                        <div class="d-none d-lg-block step-connector"></div>
                        <div class="position-relative d-inline-block">
                            <div class="step-icon-box"><i class="fa-solid fa-code-branch"></i></div>
                            <span class="step-number">03</span>
                        </div>
                        <p class="step-title">Build Family Tree</p>
                        <p class="step-sub">Connect generations with our visual tree builder</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                    <div class="step-wrap">
                        <div class="position-relative d-inline-block">
                            <div class="step-icon-box"><i class="fa-solid fa-heart"></i></div>
                            <span class="step-number">04</span>
                        </div>
                        <p class="step-title">Preserve Memories Forever</p>
                        <p class="step-sub">Save photos, voices, and stories for eternity</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════
                         ALL FEATURES
                      ═══════════════════════════════════════════ -->
    <section id="features" class="py-section">
        <div class="container">

            <div class="text-center mb-5" data-aos="fade-up">
                <span class="section-label">Everything You Need</span>
                <h2 class="section-title font-playfair mt-2">
                    Powerful Features for <span class="text-gradient">Every Family</span>
                </h2>
            </div>

            <div class="row g-4">
                <div class="col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="0">
                    <div class="feat-card">
                        <div class="feat-icon-box"><i class="fa-solid fa-shield-halved"></i></div>
                        <p class="feat-title">Secure Private Access</p>
                        <p class="feat-sub">End-to-end encryption for all your family data</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="60">
                    <div class="feat-card">
                        <div class="feat-icon-box"><i class="fa-solid fa-microphone"></i></div>
                        <p class="feat-title">Voice Memories</p>
                        <p class="feat-sub">Record and preserve precious voice recordings</p>
                    </div>
                </div>
                {{-- <div class="col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="120">
          <div class="feat-card">
            <div class="feat-icon-box"><i class="fa-solid fa-wand-magic-sparkles"></i></div>
            <p class="feat-title">AI Photo Repair</p>
            <p class="feat-sub">Restore and colorize old photos with AI</p>
          </div>
        </div> --}}
                {{-- <div class="col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="180">
          <div class="feat-card">
            <div class="feat-icon-box"><i class="fa-solid fa-globe"></i></div>
            <p class="feat-title">Global Connection</p>
            <p class="feat-sub">Unite family across continents instantly</p>
          </div>
        </div> --}}
                <div class="col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="0">
                    <div class="feat-card">
                        <div class="feat-icon-box"><i class="fa-regular fa-clock"></i></div>
                        <p class="feat-title">Family Timeline</p>
                        <p class="feat-sub">Chronological journey through your heritage</p>
                    </div>
                </div>
                {{-- <div class="col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="60">
          <div class="feat-card">
            <div class="feat-icon-box"><i class="fa-solid fa-box-archive"></i></div>
            <p class="feat-title">Heritage Archive</p>
            <p class="feat-sub">Safe storage for documents and artifacts</p>
          </div>
        </div> --}}
                {{-- <div class="col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="120">
          <div class="feat-card">
            <div class="feat-icon-box"><i class="fa-regular fa-calendar"></i></div>
            <p class="feat-title">Event Sharing</p>
            <p class="feat-sub">Plan and celebrate family moments together</p>
          </div>
        </div> --}}
                <div class="col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="180">
                    <div class="feat-card">
                        <div class="feat-icon-box"><i class="fa-solid fa-code-branch"></i></div>
                        <p class="feat-title">Multi-gen Tree</p>
                        <p class="feat-sub">Trace your lineage across generations</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════
                         TESTIMONIALS
        ═══════════════════════════════════════════ -->
    @if ($testimonials->count() > 0)
        <section id="testimonials" class="py-section">
            <div class="container">

                <div class="text-center mb-5" data-aos="fade-up">
                    <span class="section-label">Love From Families</span>
                    <h2 class="section-title font-playfair mt-2">
                        Stories That <span class="text-gradient">Warm Hearts</span>
                    </h2>
                </div>

                <div class="swiper testimonial-swiper">
                    <div class="swiper-wrapper">
                        @foreach ($testimonials as $item)
                            <div class="swiper-slide h-auto" data-aos="fade-up" data-aos-delay="0">
                                <div class="testimonial-card h-100 mb-4">
                                    <div class="quote-icon"><i class="fa-solid fa-quote-left"></i></div>
                                    <p class="testimonial-text">
                                        "{{ $item->message }}"
                                    </p>
                                    <div class="testimonial-divider"></div>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="t-avatar">
                                            <img src="{{ $item->image ? asset($item->image) : asset('assets/images/default-user.png') }}"
                                                alt="{{ $item->name }} avatar" loading="lazy">
                                        </div>
                                        <div>
                                            <p class="t-name">{{ $item->name }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </section>
    @endif

    <!-- ═══════════════════════════════════════════
                         MOBILE APP SECTION
                      ═══════════════════════════════════════════ -->
    <section class="mobile-section py-section">
        <div class="container">

            <div class="text-center mb-5" data-aos="fade-up">
                <span class="section-label">Available Everywhere</span>
                <h2 class="section-title font-playfair mt-2">
                    Your Family in <span class="text-gradient">Your Pocket</span>
                </h2>
                <p class="section-lead mt-3 mx-auto" style="max-width:500px;">
                    Access your family tree, chat with relatives, and preserve memories — all from your phone.
                </p>
            </div>

            <div class="d-flex align-items-end justify-content-center gap-4 gap-lg-5">
                <div class="phone-frame phone-side d-none d-sm-block" style="width:200px;" data-aos="fade-right"
                    data-aos-delay="100">
                    <img src="{{ asset('assets/images/family-chat-screen.png') }}" alt="Family chat app screen">
                </div>
                <div class="phone-frame" style="width:230px;position:relative;z-index:2;" data-aos="fade-up">
                    <img src="{{ asset('assets/images/family-legacy-mobile-app.png') }}" alt="Family Legacy mobile app">
                </div>
                <div class="phone-frame phone-side-right d-none d-sm-block" style="width:200px;" data-aos="fade-left"
                    data-aos-delay="100">
                    <img src="{{ asset('assets/images/ai-photo-restoration-mobile.png') }}"
                        alt="AI photo restoration mobile">
                </div>
            </div>

            <div class="d-flex flex-wrap justify-content-center gap-3 mt-5" data-aos="fade-up" data-aos-delay="200">
                @if (!empty($settings?->ios_app_url))
                    <a href="{{ $settings->ios_app_url }}" class="btn-store" target="_blank" rel="noopener noreferrer">
                        <i class="fa-brands fa-apple"></i>
                        <div>
                            <div class="btn-store-sub">Download on the</div>
                            <div class="btn-store-main">App Store</div>
                        </div>
                    </a>
                @endif
                @if (!empty($settings?->android_app_url))
                    <a href="{{ $settings->android_app_url }}" class="btn-store" target="_blank"
                        rel="noopener noreferrer">
                        <i class="fa-brands fa-google-play"></i>
                        <div>
                            <div class="btn-store-sub">Get it on</div>
                            <div class="btn-store-main">Google Play</div>
                        </div>
                    </a>
                @endif
            </div>

        </div>
    </section>

    <!-- ═══════════════════════════════════════════
                         CTA SECTION
                      ═══════════════════════════════════════════ -->
    <section class="cta-section py-section" id="cta-section">
        <div class="cta-blob-1"></div>
        <div class="cta-blob-2"></div>
        <div class="cta-blob-3"></div>

        <div class="container text-center position-relative" style="z-index:2;">
            <div data-aos="fade-up">
                <div class="cta-badge">
                    <i class="fa-solid fa-heart"></i>
                    Join 50,000+ families today
                </div>
                <h2 class="cta-title font-playfair">
                    Keep Your Family History<br>Alive For Generations
                </h2>
                <p class="cta-lead mt-3 mx-auto">
                    Don't let precious memories fade away. Start building your family's digital legacy today.
                </p>
                <div class="mt-4">
                    <a href="{{ route('register') }}" class="btn-cta-white">
                        Start Your Family Legacy Today
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

@endsection
