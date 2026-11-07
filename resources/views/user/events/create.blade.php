<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .event-form-page {
        padding: 20px;
    }

    .flatpickr-input {
        cursor: pointer;
    }

    .event-form-card {
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        background: #fff;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .event-form-header {
        padding: 20px;
        border-bottom: 1px solid #e5e7eb;
    }

    .event-form-body {
        padding: 20px;
    }

    .form-section {
        margin-bottom: 20px;
    }

    .form-group label {
        font-size: 0.85rem;
        font-weight: 500;
        color: #374151;
        margin-bottom: 6px;
        display: block;
    }

    .form-control,
    .form-select {
        border-radius: 8px;
        padding: 10px 14px;
        border: 1px solid #d1d5db;
        font-size: 0.95rem;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #7c3aed;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.2);
    }

    .form-control.is-invalid,
    .form-select.is-invalid {
        border-color: #ef4444;
    }

    .invalid-feedback {
        display: block;
        margin-top: 4px;
    }

    .textarea-control {
        min-height: 120px;
        resize: vertical;
    }

    .btn-submit {
        padding: 10px 24px;
        border-radius: 8px;
        font-size: 0.95rem;
        font-weight: 500;
        background-color: #7c3aed;
        border-color: #7c3aed;
    }

    .btn-submit:hover {
        background-color: #6d28d9;
        border-color: #6d28d9;
    }

    /* Cover Image Upload Box Fixes */
    .cover-upload-box {
        position: relative;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 100%;
        height: 180px;
        border: 2px dashed #d1d5db;
        border-radius: 10px;
        overflow: hidden;
        cursor: pointer;
        background-color: #f9fafb;
        transition: all 0.2s ease-in-out;
        margin-bottom: 0;
    }

    .cover-upload-box:hover {
        border-color: #7c3aed;
        background-color: #f3f4f6;
    }

    .cover-preview-img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: 1;
    }

    .upload-placeholder {
        position: relative;
        z-index: 2;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        padding: 15px;
        text-align: center;
        transition: all 0.2s ease;
    }

    .cover-upload-box.has-image .upload-placeholder {
        opacity: 0;
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(2px);
    }

    .cover-upload-box.has-image:hover .upload-placeholder {
        opacity: 1;
    }
</style>

<form action="{{ isset($event) ? route('user.events.update', $event->id) : route('user.events.store') }}" method="POST"
    enctype="multipart/form-data" id="eventForm">
    @csrf
    @if (isset($event))
        @method('PUT')
    @endif

    <div class="event-form-body">
        <!-- Event Type -->
        <div class="form-section">
            <div class="form-group">
                <label for="type">Event Type *</label>
                <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                    <option value="">Select event type</option>
                    @foreach (App\Models\Event::getTypes() as $type)
                        <option value="{{ $type }}"
                            {{ old('type', $event->type ?? '') === $type ? 'selected' : '' }}>
                            {{ ucwords(str_replace('_', ' ', $type)) }}
                        </option>
                    @endforeach
                </select>
                @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Basic Details -->
        <div class="form-section">
            <div class="row g-3">
                <div class="col-12">
                    <div class="form-group">
                        <label for="title">Event Title *</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                            name="title" value="{{ old('title', $event->title ?? '') }}" required maxlength="255"
                            placeholder="Enter event title">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label for="event_date">Event Date *</label>
                        <input type="text"
                            class="form-control js-date-picker @error('event_date') is-invalid @enderror"
                            id="event_date" name="event_date"
                            value="{{ old('event_date', isset($event->event_date) ? \Carbon\Carbon::parse($event->event_date)->format('Y-m-d H:i') : '') }}"
                            required placeholder="Select date and time">
                        @error('event_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" class="form-control @error('location') is-invalid @enderror"
                            id="location" name="location" value="{{ old('location', $event->location ?? '') }}"
                            maxlength="500" placeholder="Enter location (optional)">
                        @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Description -->
        <div class="form-section">
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control textarea-control @error('description') is-invalid @enderror" id="description"
                    name="description" maxlength="5000" placeholder="Add details about this event...">{{ old('description', $event->description ?? '') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted mt-1 d-block">Add a description, agenda, or notes for this event.</small>
            </div>
        </div>

        <!-- Cover Image Upload Box -->
        <div class="form-section">
            <div class="form-group">
                <label>Cover Image</label>
                <input type="file" class="d-none" id="cover_image_input" name="cover_image" accept="image/*">

                @php
                    $hasCover = isset($event) && !empty($event->cover_image);
                    $coverUrl = $hasCover ? asset($event->cover_image) : '';
                @endphp

                <label for="cover_image_input" class="cover-upload-box {{ $hasCover ? 'has-image' : '' }}"
                    id="cover_box">
                    <!-- Image Preview -->
                    <img src="{{ $coverUrl }}" alt="Cover Preview"
                        class="cover-preview-img {{ $hasCover ? '' : 'd-none' }}" id="cover_preview">

                    <!-- Perfectly Centered Placeholder -->
                    <div class="upload-placeholder" id="upload_placeholder">
                        <i class="fa-solid fa-cloud-arrow-up fa-2x text-muted mb-2"></i>
                        <div class="fw-medium text-dark" id="upload_text">
                            {{ $hasCover ? 'Click to change cover image' : 'Click to upload cover image' }}
                        </div>
                        <small class="text-muted mt-1">JPG, PNG, WEBP max 4MB</small>
                    </div>
                </label>
                @error('cover_image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="text-end">
        <button type="submit" class="btn btn-primary btn-submit">
            <i class="fa-solid fa-floppy-disk me-2"></i>{{ isset($event) ? 'Update Event' : 'Create Event' }}
        </button>
    </div>
</form>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    (function initEventFormScript() {
        // Flatpickr initialization
        const dateInput = document.getElementById('event_date');
        if (window.flatpickr && dateInput) {
            flatpickr('#event_date', {
                enableTime: true,
                dateFormat: 'Y-m-d H:i',
                time_24hr: true,
                minDate: 'today'
            });
        }

        // Inside Box Image Preview Logic
        const coverInput = document.getElementById('cover_image_input');
        const preview = document.getElementById('cover_preview');
        const coverBox = document.getElementById('cover_box');
        const uploadText = document.getElementById('upload_text');

        if (coverInput && preview && coverBox) {
            coverInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.classList.remove('d-none');
                        coverBox.classList.add('has-image');
                        if (uploadText) {
                            uploadText.textContent = 'Click to change cover image';
                        }
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }

        // Form Submit Validation
        const eventForm = document.getElementById('eventForm');
        if (eventForm) {
            eventForm.addEventListener('submit', function(e) {
                const typeSelect = document.getElementById('type');

                if (!typeSelect || !typeSelect.value) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Validation Error',
                        text: 'Please select an event type',
                        icon: 'warning',
                        confirmButtonColor: '#7c3aed'
                    });
                }
            });
        }
    })();
</script>
