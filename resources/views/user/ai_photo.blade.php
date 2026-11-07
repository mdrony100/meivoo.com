@extends('layouts.user')

@section('title', 'AI Photo Restoration')

@push('styles')
    <style>
        /* ==========================================
                                                   1. BASE STYLES
                                                   ========================================== */
        .ai-full-wrapper {
            width: 100%;
            padding: 24px;
            box-sizing: border-box;
        }

        .ai-hero-banner {
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
            color: #ffffff;
            padding: 24px 28px;
            border-radius: 16px;
            margin-bottom: 24px;
            box-shadow: 0 10px 25px -5px rgba(124, 58, 237, 0.25);
        }

        .hero-content {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .hero-icon {
            background: rgba(255, 255, 255, 0.2);
            padding: 12px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .hero-content h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
        }

        .hero-content p {
            margin: 4px 0 0 0;
            font-size: 13px;
            opacity: 0.9;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .alert-danger {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .ai-main-grid {
            display: grid;
            grid-template-columns: 2.2fr 1fr;
            gap: 24px;
        }

        .ai-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        }

        .card-section-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
        }

        .step-num {
            background: #f3e8ff;
            color: #7c3aed;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .card-section-header h3 {
            font-size: 16px;
            font-weight: 600;
            color: #111827;
            margin: 0;
        }

        /* Tools Grid Styling */
        .tools-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-bottom: 24px;
        }

        .tool-radio-label {
            cursor: pointer;
            display: block;
        }

        .tool-card {
            border: 1.5px solid #e5e7eb;
            padding: 14px 16px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
            background: #ffffff;
            transition: all 0.2s ease-in-out;
        }

        .tool-icon-box {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background-color: #f3f4f6;
            color: #4b5563;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.2s ease-in-out;
        }

        .tool-info h4 {
            margin: 0;
            font-size: 14px;
            color: #1f2937;
            font-weight: 600;
        }

        .tool-info p {
            margin: 2px 0 0 0;
            font-size: 12px;
            color: #6b7280;
        }

        .tool-card:hover {
            border-color: #d8b4fe;
            background-color: #faf5ff;
        }

        .tool-card:hover .tool-icon-box {
            background-color: #f3e8ff;
            color: #7c3aed;
        }

        .tool-radio-label input[type="radio"]:checked+.tool-card {
            border-color: #7c3aed;
            background-color: #faf5ff;
            box-shadow: 0 4px 12px rgba(124, 58, 237, 0.08);
        }

        .tool-radio-label input[type="radio"]:checked+.tool-card .tool-icon-box {
            background-color: #7c3aed;
            color: #ffffff;
        }

        /* Before & After UI Design */
        .comparison-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 20px;
        }

        .comparison-column {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        .column-title {
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .label-before {
            color: #5b5b7c;
        }

        .label-after {
            color: #7c3aed;
        }

        .preview-card {
            position: relative;
            width: 100%;
            height: 260px;
            border-radius: 20px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
        }

        .card-before {
            background-color: #f3f4f6;
            border: 2px dashed #d1d5db;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .card-before:hover {
            border-color: #7c3aed;
            background-color: #faf5ff;
        }

        .card-before img,
        .card-after img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 18px;
        }

        .upload-placeholder {
            text-align: center;
            padding: 16px;
        }

        .upload-icon-circle {
            width: 48px;
            height: 48px;
            background: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        }

        .upload-text {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
        }

        .upload-subtext {
            display: block;
            font-size: 11px;
            color: #6b7280;
            margin-top: 2px;
        }

        .hover-change-overlay {
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(4px);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 16px;
            opacity: 0;
            transition: opacity 0.2s ease-in-out;
            border-radius: 18px;
            pointer-events: none;
            z-index: 3;
        }

        .card-before.has-image:hover .hover-change-overlay {
            opacity: 1;
        }

        .card-after {
            background-color: #faf5ff;
            border: 2px solid #7c3aed;
        }

        .processing-overlay {
            position: absolute;
            inset: 0;
            background: rgba(250, 245, 255, 0.95);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 5;
        }

        .spinner {
            width: 32px;
            height: 32px;
            border: 3px solid #e9d5ff;
            border-top: 3px solid #7c3aed;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .processing-title {
            margin-top: 10px;
            font-size: 13px;
            font-weight: 600;
            color: #7c3aed;
        }

        /* Action Bar Buttons */
        .action-bar-wrapper {
            margin-top: 12px;
        }

        .action-row {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-process-sm {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background-color: #7c3aed;
            color: #ffffff;
            border: none;
            padding: 11px 22px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-process-sm svg {
            flex-shrink: 0;
            filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.15));
        }

        .btn-process-sm:hover {
            background-color: #6d28d9;
        }

        .btn-download-sm {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background-color: #10b981;
            color: #ffffff;
            padding: 11px 18px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.2s;
        }

        .btn-download-sm:hover {
            background-color: #059669;
        }

        /* Progress Bar */
        .progress-box {
            width: 100%;
            margin-top: 12px;
        }

        .progress-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
            font-size: 12px;
            font-weight: 500;
        }

        .progress-status {
            color: #4b5563;
        }

        .progress-percent {
            color: #7c3aed;
            font-weight: 600;
        }

        .progress-bar-bg {
            width: 100%;
            height: 8px;
            background-color: #f3e8ff;
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-bar-fill {
            width: 0%;
            height: 100%;
            background: linear-gradient(90deg, #7c3aed 0%, #a855f7 100%);
            border-radius: 10px;
            transition: width 0.3s ease;
        }

        /* Sidebar Info Card */
        .info-card h3 {
            margin: 0 0 16px 0;
            font-size: 16px;
            color: #111827;
        }

        .guide-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .guide-list li {
            margin-bottom: 16px;
            font-size: 13px;
            color: #4b5563;
            line-height: 1.5;
        }

        .guide-list li strong {
            display: block;
            color: #111827;
            margin-bottom: 2px;
        }

        .divider {
            border: 0;
            border-top: 1px solid #f3f4f6;
            margin: 20px 0;
        }

        .support-box h4 {
            margin: 0 0 4px 0;
            font-size: 14px;
            color: #111827;
        }

        .support-box p {
            margin: 0;
            font-size: 12px;
            color: #6b7280;
        }


        .legacy-pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 40px;
        }

        /* Base list resets */
        .legacy-pagination .pagination {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 0;
            margin: 0;
            list-style: none;
        }

        /* Each item */
        .legacy-pagination .page-item {
            margin: 0;
        }

        /* Default state for numbers & arrows */
        .legacy-pagination .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 42px;
            height: 42px;
            padding: 0 14px;

            color: #4f5667;
            background-color: #ffffff;
            border: 1px solid #edf0f5;
            border-radius: 12px;

            font-size: 14px;
            font-weight: 600;
            text-decoration: none;

            box-shadow: 0 2px 6px rgba(31, 41, 55, 0.04);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Hover state */
        .legacy-pagination .page-link:hover {
            color: #6366f1;
            background-color: #f4f5ff;
            border-color: #d8dafe;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(99, 102, 241, 0.12);
        }

        /* Active page state */
        .legacy-pagination .page-item.active .page-link {
            color: #ffffff !important;
            background: linear-gradient(135deg, #6366f1, #8b5cf6) !important;
            border-color: transparent !important;
            font-weight: 700;
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.35) !important;
        }

        /* Disabled state (e.g., Previous on first page) */
        .legacy-pagination .page-item.disabled .page-link {
            color: #c0c6d4;
            background-color: #f8fafc;
            border-color: #edf0f5;
            cursor: not-allowed;
            opacity: 0.7;
            box-shadow: none;
            transform: none;
        }

        /* Focus state for accessibility */
        .legacy-pagination .page-link:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25);
        }


        /* ==========================================
                                                   2. MOBILE RESPONSIVE MEDIA QUERIES
                                                   ========================================== */

        /* Tablet & Mobile (<= 1024px) */
        @media (max-width: 1024px) {
            .ai-main-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }
        }

        /* Mobile Devices (<= 768px) */
        @media (max-width: 768px) {
            .ai-full-wrapper {
                padding: 12px;
            }

            .ai-hero-banner {
                padding: 16px;
                border-radius: 12px;
                margin-bottom: 16px;
            }

            .hero-content {
                gap: 12px;
            }

            .hero-icon {
                padding: 8px;
                border-radius: 10px;
            }

            .hero-icon svg {
                width: 24px;
                height: 24px;
            }

            .hero-content h2 {
                font-size: 17px;
            }

            .hero-content p {
                font-size: 12px;
            }

            .ai-card {
                padding: 16px;
                border-radius: 14px;
            }

            /* Stack Before/After Cards vertically on mobile */
            .comparison-container {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .preview-card {
                height: 220px;
                border-radius: 16px;
            }

            /* Full Width Buttons on Mobile */
            .action-row {
                flex-direction: column;
                width: 100%;
                gap: 10px;
            }

            .btn-process-sm,
            .btn-download-sm {
                width: 100%;
                padding: 12px;
                font-size: 14px;
            }
        }

        /* Extra Small Phones (<= 480px) */
        @media (max-width: 480px) {

            /* Tool Cards 1 column for clean view */
            .tools-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .tool-card {
                padding: 12px;
            }

            .preview-card {
                height: 200px;
            }
        }

        @media (min-width: 576px) {
            .responsive-dl-btn {
                width: auto !important;
            }
        }

        @media (max-width: 575.98px) {
            .responsive-dl-btn {
                width: 100% !important;
                padding: 10px auto !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="ai-full-wrapper">

        <!-- Hero Banner -->
        <div class="ai-hero-banner">
            <div class="hero-content">
                <div class="hero-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path
                            d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3L12 3z" />
                    </svg>
                </div>
                <div>
                    <h2>AI Photo Restoration & Enhancer</h2>
                    <p>Revive old photos, colorize memories, and remove scratches automatically.</p>
                </div>
            </div>
        </div>

        <!-- Main Grid Section -->
        <div class="ai-main-grid">

            <!-- LEFT COLUMN: Form & Before/After Card -->
            <div class="grid-left">
                <form action="{{ route('user.ai-photo.process') }}" method="POST" enctype="multipart/form-data"
                    class="ai-card" id="aiProcessForm">
                    @csrf

                    <!-- Section 1: AI Tools Selection -->
                    <div class="card-section-header">
                        <span class="step-num">1</span>
                        <h3>Select AI Feature</h3>
                    </div>

                    <div class="tools-grid">
                        <!-- Tool 1: Restore Photo -->
                        <label class="tool-radio-label">
                            <input type="radio" name="tool_type" value="restore" checked hidden>
                            <div class="tool-card">
                                <div class="tool-icon-box">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="m19 2 2 2-2 2-2-2 2-2Z" />
                                        <path d="m5 11 2 2-2 2-2-2 2-2Z" />
                                        <path d="m19 15 1.5 1.5-1.5 1.5-1.5-1.5 1.5-1.5Z" />
                                        <path d="M14 4 4.5 13.5a2.121 2.121 0 0 0 3 3L17 7" />
                                    </svg>
                                </div>
                                <div class="tool-info">
                                    <h4>Restore Photo</h4>
                                    <p>Fix damage & enhance</p>
                                </div>
                            </div>
                        </label>

                        <!-- Tool 2: Colorize -->
                        <label class="tool-radio-label">
                            <input type="radio" name="tool_type" value="colorize" hidden>
                            <div class="tool-card">
                                <div class="tool-icon-box">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <circle cx="13.5" cy="6.5" r=".5" fill="currentColor" />
                                        <circle cx="17.5" cy="10.5" r=".5" fill="currentColor" />
                                        <circle cx="8.5" cy="7.5" r=".5" fill="currentColor" />
                                        <circle cx="6.5" cy="12.5" r=".5" fill="currentColor" />
                                        <path
                                            d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.92 0 1.7-.75 1.7-1.67 0-.42-.16-.82-.44-1.12-.27-.3-.43-.7-.43-1.13 0-.92.75-1.67 1.67-1.67H16c3.3 0 6-2.7 6-6 0-4.73-4.03-8.42-8.8-8.42Z" />
                                    </svg>
                                </div>
                                <div class="tool-info">
                                    <h4>Colorize</h4>
                                    <p>Add color to B&W</p>
                                </div>
                            </div>
                        </label>

                        <!-- Tool 3: Remove Blur -->
                        <label class="tool-radio-label">
                            <input type="radio" name="tool_type" value="remove_blur" hidden>
                            <div class="tool-card">
                                <div class="tool-icon-box">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="3" />
                                        <path d="M3 7V5a2 2 0 0 1 2-2h2" />
                                        <path d="M17 3h2a2 2 0 0 1 2 2v2" />
                                        <path d="M21 17v2a2 2 0 0 1-2 2h-2" />
                                        <path d="M7 21H5a2 2 0 0 1-2-2v-2" />
                                    </svg>
                                </div>
                                <div class="tool-info">
                                    <h4>Remove Blur</h4>
                                    <p>Sharpen details</p>
                                </div>
                            </div>
                        </label>

                        <!-- Tool 4: Clean Damage -->
                        <label class="tool-radio-label">
                            <input type="radio" name="tool_type" value="clean_damage" hidden>
                            <div class="tool-card">
                                <div class="tool-icon-box">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path
                                            d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3L12 3z" />
                                    </svg>
                                </div>
                                <div class="tool-info">
                                    <h4>Clean Damage</h4>
                                    <p>Remove scratches</p>
                                </div>
                            </div>
                        </label>
                    </div>

                    <!-- Section 2: Before & After Interactive Container -->
                    <div class="card-section-header">
                        <span class="step-num">2</span>
                        <h3>Upload & Compare</h3>
                    </div>

                    @php
                        $hasSessionPhoto = session('latestPhoto') && session('latestPhoto')->original_path;
                        $hasEnhancedPhoto = session('latestPhoto') && session('latestPhoto')->enhanced_path;
                    @endphp

                    <input type="file" name="photo" id="photoInput" {{ $hasSessionPhoto ? '' : 'required' }}
                        accept="image/png, image/jpeg, image/jpg" hidden>

                    <div class="comparison-container">
                        <!-- Before Card (Clickable to Upload) -->
                        <div class="comparison-column">
                            <span class="column-title label-before">Before</span>
                            <div class="preview-card card-before {{ $hasSessionPhoto ? 'has-image' : '' }}"
                                id="beforeCardBox">
                                <img id="beforeImgPreview"
                                    src="{{ $hasSessionPhoto ? asset('storage/' . session('latestPhoto')->original_path) : '' }}"
                                    alt="Before Preview"
                                    style="{{ $hasSessionPhoto ? 'display: block;' : 'display: none;' }}">

                                <!-- Hover Overlay Layout -->
                                <div class="hover-change-overlay">
                                    <div class="upload-icon-circle">
                                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none"
                                            stroke="#7c3aed" stroke-width="2">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                            <polyline points="17 8 12 3 7 8" />
                                            <line x1="12" y1="3" x2="12" y2="15" />
                                        </svg>
                                    </div>
                                    <span class="upload-text">Change Image</span>
                                    <span class="upload-subtext">JPG, PNG up to 10MB</span>
                                </div>

                                <!-- Initial Upload Placeholder -->
                                <div class="upload-placeholder" id="uploadPlaceholder"
                                    style="{{ $hasSessionPhoto ? 'display: none;' : 'display: flex;' }} flex-direction: column; align-items: center; justify-content: center; text-align: center; width: 100%; height: 100%;">

                                    <div class="upload-icon-circle"
                                        style="background: #f1f5f9; padding: 12px; border-radius: 50%; margin-bottom: 8px; display: flex; align-items: center; justify-content: center;">
                                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none"
                                            stroke="#7c3aed" stroke-width="2">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                            <polyline points="17 8 12 3 7 8" />
                                            <line x1="12" y1="3" x2="12" y2="15" />
                                        </svg>
                                    </div>

                                    <span class="upload-text"
                                        style="display: block; font-weight: 600; color: #334155; font-size: 14px;">Click to
                                        Select Photo</span>
                                    <span class="upload-subtext"
                                        style="display: block; color: #64748b; font-size: 12px; margin-top: 4px;">JPG, PNG
                                        up to 10MB</span>
                                </div>
                            </div>
                        </div>

                        <!-- After Card -->
                        <div class="comparison-column">
                            <span class="column-title label-after">After</span>
                            <div class="preview-card card-after" id="afterCardBox">
                                @if (session('latestPhoto') && session('latestPhoto')->enhanced_path)
                                    <img src="{{ asset('storage/' . session('latestPhoto')->enhanced_path) }}"
                                        alt="After Result">
                                @else
                                    <div class="after-placeholder" id="afterPlaceholder">
                                        <svg class="sparkle-icon" width="40" height="40" viewBox="0 0 24 24"
                                            fill="none" stroke="#7c3aed" stroke-width="1.8">
                                            <path
                                                d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3L12 3z" />
                                        </svg>
                                    </div>
                                @endif

                                <!-- Processing Overlay (Hidden by Default) -->
                                <div class="processing-overlay" id="processingOverlay" style="display: none;">
                                    <div class="spinner"></div>
                                    <span class="processing-title">Processing...</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Bar & Progress Bar -->
                    <div class="action-bar-wrapper">
                        <div class="action-row">
                            <!-- Process & Enhance Button -->
                            <button type="submit" id="submitBtn" class="btn-process-sm mt-2"
                                style="{{ $hasEnhancedPhoto ? 'display: none;' : 'display: flex;' }}">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2L14.5 9.5L22 12L14.5 14.5L12 22L9.5 14.5L2 12L9.5 9.5L12 2Z" />
                                </svg>
                                <span>Process & Enhance</span>
                            </button>

                            <!-- Download Button -->
                            @if ($hasEnhancedPhoto)
                                <a href="{{ route('user.ai-photo.download', session('latestPhoto')->id) }}"
                                    id="downloadBtn" class="btn-download-sm mt-2">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                        <polyline points="7 10 12 15 17 10" />
                                        <line x1="12" y1="15" x2="12" y2="3" />
                                    </svg>
                                    Download Image
                                </a>
                            @endif
                        </div>

                        <!-- Animated Progress Bar -->
                        <div id="progressBox" class="progress-box" style="display: none;">
                            <div class="progress-info">
                                <span class="progress-status">Enhancing image with AI...</span>
                                <span id="progressPercent" class="progress-percent">0%</span>
                            </div>
                            <div class="progress-bar-bg">
                                <div id="progressBar" class="progress-bar-fill"></div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- RIGHT COLUMN: Guidelines -->
            <div class="grid-right">
                <div class="ai-card info-card">
                    <h3>💡 Quick Guidelines</h3>
                    <ul class="guide-list">
                        <li>
                            <strong>Select Photo:</strong>
                            <span>Click on the 'Before' card to select your photo from your device.</span>
                        </li>
                        <li>
                            <strong>Select Tool:</strong>
                            <span>Choose whether you want to Restore, Colorize, Sharpen or Clean scratches.</span>
                        </li>
                        <li>
                            <strong>Process & Download:</strong>
                            <span>Click Process and watch the live transformation happen in seconds.</span>
                        </li>
                    </ul>

                    <hr class="divider">

                    <div class="support-box">
                        <h4>Need Help?</h4>
                        <p>Supported file formats are PNG and JPG up to 10MB.</p>
                    </div>
                </div>
            </div>

        </div>

        {{-- Section 3: History / Generation Logs --}}
        @if (isset($historyPhotos) && $historyPhotos->count() > 0)
            <div class="card p-4 mt-5 shadow-sm">
                <div class="mb-4">
                    <h5><i class="fa-solid fa-history me-1 fs-6" style="color: var(--primary);"></i> Your Recent
                        Generations</h5>
                </div>

                <div class="row g-3">
                    @foreach ($historyPhotos as $photo)
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden position-relative">

                                {{-- Tool Badge --}}
                                <span class="badge btn-post position-absolute top-0 start-0 m-2 text-uppercase"
                                    style="font-size: 10px; z-index: 2;">
                                    {{ str_replace('_', ' ', $photo->tool_type) }}
                                </span>

                                {{-- Image Display --}}
                                <div class="ratio ratio-1x1 bg-light">
                                    <img src="{{ asset('storage/' . $photo->enhanced_path) }}" alt="Enhanced Result"
                                        class="card-img-top object-fit-cover">
                                </div>

                                <div
                                    class="card-body p-2 d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center gap-2 bg-white">
                                    <small class="text-muted text-center text-sm-start" style="font-size: 14px;">
                                        {{ $photo->created_at->format('M d, Y') }}
                                    </small>

                                    <a href="{{ route('user.ai-photo.download', $photo->id) }}"
                                        class="btn-download-sm py-1.5 px-3 d-flex justify-content-center align-items-center gap-1 responsive-dl-btn"
                                        title="Download Image" style="font-size: 11px;">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                            <polyline points="7 10 12 15 17 10" />
                                            <line x1="12" y1="15" x2="12" y2="3" />
                                        </svg>
                                        <span>Download</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination Links --}}
                @if ($historyPhotos->hasPages())
                    <div class="legacy-pagination">
                        {{ $historyPhotos->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>
        @endif

    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const photoInput = document.getElementById('photoInput');
            const beforeCardBox = document.getElementById('beforeCardBox');
            const beforeImgPreview = document.getElementById('beforeImgPreview');
            const uploadPlaceholder = document.getElementById('uploadPlaceholder');

            const aiForm = document.getElementById('aiProcessForm');
            const submitBtn = document.getElementById('submitBtn');
            const processingOverlay = document.getElementById('processingOverlay');
            const progressBox = document.getElementById('progressBox');
            const progressBar = document.getElementById('progressBar');
            const progressPercent = document.getElementById('progressPercent');
            const downloadBtn = document.getElementById('downloadBtn');

            // 1. Click Before Card to Open File Selector
            if (beforeCardBox && photoInput) {
                beforeCardBox.addEventListener('click', function() {
                    photoInput.click();
                });
            }

            // 2. Live Preview in Before Card & Enable Hover Overlay
            if (photoInput) {
                photoInput.addEventListener('change', function(e) {
                    if (e.target.files && e.target.files[0]) {
                        const reader = new FileReader();

                        reader.onload = function(e) {
                            beforeImgPreview.src = e.target.result;
                            beforeImgPreview.style.display = 'block';
                            uploadPlaceholder.style.display = 'none';

                            beforeCardBox.classList.add('has-image');
                        }

                        reader.readAsDataURL(e.target.files[0]);

                        if (submitBtn) {
                            submitBtn.style.display = 'flex';
                        }
                    }
                });
            }

            if (aiForm) {
                aiForm.addEventListener('submit', function(e) {
                    if (!photoInput.files || photoInput.files.length === 0) {
                        const hasSession = "{{ session('latestPhoto') ? 'yes' : 'no' }}" === 'yes';
                        if (!hasSession) {
                            e.preventDefault();
                            alert('Please select an image first.');
                            return;
                        }
                    }

                    if (processingOverlay) {
                        processingOverlay.style.display = 'flex';
                    }

                    if (submitBtn) submitBtn.style.display = 'none';

                    if (downloadBtn) {
                        downloadBtn.style.display = 'none';
                    }

                    if (progressBox) progressBox.style.display = 'block';

                    let width = 0;
                    const interval = setInterval(function() {
                        if (width >= 90) {
                            clearInterval(interval);
                        } else {
                            width += Math.floor(Math.random() * 8) + 4;
                            if (width > 90) width = 90;
                            if (progressBar) progressBar.style.width = width + '%';
                            if (progressPercent) progressPercent.innerText = width + '%';
                        }
                    }, 250);
                });
            }
        });
    </script>
@endpush
