<style>
    .legacy-modal-container {
        font-family: inherit;
        color: #0f172a;
        padding: 4px;
        box-sizing: border-box;
    }

    /* Keyframe Animations */
    @keyframes pulseGlow {
        0%, 100% {
            transform: scale(1);
            opacity: 0.6;
        }
        50% {
            transform: scale(1.18);
            opacity: 0.2;
        }
    }

    @keyframes floatIcon {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-6px); }
    }

    /* Modern Hero Banner with Dot Pattern + Glass Card */
    .legacy-modal-banner {
        position: relative;
        padding: 36px 20px 28px;
        border-radius: 24px;
        border: 1px solid rgba(224, 231, 255, 0.8);
        margin-bottom: 24px;
        text-align: center;
        overflow: hidden;

        /* Base Dot Pattern Matched with Single Card */
        background-color: #f7f6ff;
        background-image: radial-gradient(rgba(124, 58, 237, 0.14) 1.5px, transparent 1.5px);
        background-size: 18px 18px;
        box-shadow: 0 10px 30px -10px rgba(124, 58, 237, 0.08);
    }

    /* Inner Glass Container inside Banner */
    .legacy-banner-glass {
        position: relative;
        z-index: 2;
        display: inline-block;
        width: 100%;
        max-width: 90%;
        padding: 20px;
        background: rgba(255, 255, 255, 0.65);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.9);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
    }

    /* Animated Icon Wrapper with Back-glow */
    .legacy-icon-wrapper {
        position: relative;
        width: 64px;
        height: 64px;
        margin: 0 auto 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .legacy-icon-wrapper::before {
        content: '';
        position: absolute;
        inset: -4px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(124, 58, 237, 0.4) 0%, transparent 70%);
        animation: pulseGlow 3s ease-in-out infinite;
    }

    .legacy-main-icon {
        position: relative;
        z-index: 2;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        box-shadow: 0 8px 20px rgba(124, 58, 237, 0.3);
        animation: floatIcon 4s ease-in-out infinite;
    }

    .legacy-banner-glass span {
        display: inline-block;
        color: #6366f1;
        font-size: 10.5px;
        font-weight: 800;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .legacy-banner-glass h2 {
        margin: 0;
        color: #0f172a;
        font-size: 28px;
        font-weight: 800;
        letter-spacing: -0.5px;
    }

    /* Sleek Modern Info Grid */
    .legacy-modal-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
        gap: 12px;
        margin-bottom: 24px;
    }

    .legacy-info-pill {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .legacy-info-pill:hover {
        border-color: #8b5cf6;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(139, 92, 246, 0.08);
    }

    .legacy-info-pill-icon {
        width: 36px;
        height: 36px;
        border-radius: 12px;
        background: #f3f0ff;
        color: #7c3aed;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        flex-shrink: 0;
        transition: all 0.3s ease;
    }

    .legacy-info-pill:hover .legacy-info-pill-icon {
        background: linear-gradient(135deg, #7c3aed, #6366f1);
        color: #ffffff;
        transform: scale(1.05);
    }

    .legacy-info-pill-data span {
        display: block;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #94a3b8;
        margin-bottom: 2px;
    }

    .legacy-info-pill-data strong {
        display: block;
        font-size: 13.5px;
        font-weight: 700;
        color: #1e2937;
        word-break: break-word;
    }

    /* Modern Minimal Sections */
    .legacy-modal-section {
        margin-bottom: 20px;
    }

    .legacy-section-head {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 10px;
    }

    .legacy-section-head i {
        color: #7c3aed;
        font-size: 14px;
    }

    .legacy-section-head h4 {
        margin: 0;
        font-size: 14px;
        font-weight: 700;
        color: #334155;
    }

    .legacy-content-card {
        padding: 16px 18px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    /* Left Subtle Color Accent Line */
    .legacy-content-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, #7c3aed, #6366f1);
    }

    .legacy-content-card.story-style::before {
        background: linear-gradient(180deg, #ec4899, #8b5cf6);
    }

    .legacy-content-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.03);
    }

    .legacy-content-card p {
        margin: 0;
        color: #475569;
        font-size: 14px;
        line-height: 1.7;
        white-space: pre-line;
    }

    /* Minimal Quote Footer */
    .legacy-quote-footer {
        margin-top: 24px;
        padding: 14px;
        text-align: center;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        color: #64748b;
        font-size: 12.5px;
        font-weight: 500;
        font-style: italic;
    }

    .legacy-quote-footer i {
        color: #a855f7;
        font-size: 10px;
        margin: 0 4px;
    }

    @media (max-width: 576px) {
        .legacy-modal-banner {
            padding: 24px 12px 20px;
        }

        .legacy-banner-glass {
            max-width: 100%;
            padding: 16px 12px;
        }

        .legacy-banner-glass h2 {
            font-size: 22px;
        }

        .legacy-modal-info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="legacy-modal-container">

    {{-- Refined Glass & Dot Hero Banner --}}
    <div class="legacy-modal-banner">
        <div class="legacy-banner-glass">
            <div class="legacy-icon-wrapper">
                <div class="legacy-main-icon">
                    <i class="fa-solid fa-feather-pointed"></i>
                </div>
            </div>
            <span>Family Legacy</span>
            <h2>{{ $legacyName->name }}</h2>
        </div>
    </div>

    {{-- Modern Info Pills Grid --}}
    <div class="legacy-modal-info-grid">

        <div class="legacy-info-pill">
            <div class="legacy-info-pill-icon">
                <i class="fa-solid fa-gift"></i>
            </div>
            <div class="legacy-info-pill-data">
                <span>Gift Name</span>
                <strong>{{ $legacyName->name }}</strong>
            </div>
        </div>

        @if ($legacyName->who_gave_this_name)
            <div class="legacy-info-pill">
                <div class="legacy-info-pill-icon">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <div class="legacy-info-pill-data">
                    <span>Given By</span>
                    <strong>{{ $legacyName->who_gave_this_name }}</strong>
                </div>
            </div>
        @endif

        @if ($legacyName->family_generation)
            <div class="legacy-info-pill">
                <div class="legacy-info-pill-icon">
                    <i class="fa-solid fa-people-group"></i>
                </div>
                <div class="legacy-info-pill-data">
                    <span>Generation</span>
                    <strong>{{ $legacyName->family_generation }}</strong>
                </div>
            </div>
        @endif

    </div>

    {{-- Meaning Section --}}
    @if ($legacyName->meaning)
        <div class="legacy-modal-section">
            <div class="legacy-section-head">
                <i class="fa-solid fa-book-open"></i>
                <h4>Meaning Behind the Name</h4>
            </div>
            <div class="legacy-content-card">
                <p>{{ $legacyName->meaning }}</p>
            </div>
        </div>
    @endif

    {{-- Story Section --}}
    @if ($legacyName->story_behind_the_name)
        <div class="legacy-modal-section">
            <div class="legacy-section-head">
                <i class="fa-solid fa-heart"></i>
                <h4>Story Behind the Name</h4>
            </div>
            <div class="legacy-content-card story-style">
                <p>{{ $legacyName->story_behind_the_name }}</p>
            </div>
        </div>
    @endif

    {{-- Footer Quote --}}
    <div class="legacy-quote-footer">
        <i class="fa-solid fa-quote-left"></i>
        Every name carries a story. Every story becomes a legacy.
        <i class="fa-solid fa-quote-right"></i>
    </div>

</div>