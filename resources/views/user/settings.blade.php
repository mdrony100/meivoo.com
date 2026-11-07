@extends('layouts.user')
@section('title', 'Profile Settings - Family Feed')
@section('body_class', 'settings-page')
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .flatpickr-input[readonly] {
            background-color: #fff;
            cursor: pointer;
        }
    </style>
@endpush
@section('content')
    <main class="main-content p-0">
        {{-- <div class="settings-page p-0">
            <div class="settings-inner"> --}}
        <div class="card p-3 rounded-4 border-0">
            <div class="row g-4 justify-content-center align-items-center">
                <div class="col-12">

                    <div class="mb-4">
                        <h1 class="page-title">Profile Settings</h1>
                        <p class="page-sub">Manage your profile information</p>
                    </div>

                    <div class="settings-card">
                        <h2 class="card-title">Update Information</h2>
                        <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <!-- Avatar row -->
                            <div class="card border border-light-subtle bg-light rounded-4 p-3 mb-4">
                                <div
                                    class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">

                                    {{-- Avatar & User Details --}}
                                    <div
                                        class="d-flex align-items-center gap-3 w-100 w-md-auto text-center text-md-start flex-column flex-sm-row">
                                        <div class="avatar-wrap flex-shrink-0" id="avatarPreviewContainer">
                                            @if (auth()->user()->image)
                                                <img src="{{ getPhoto(auth()->user()->image) }}"
                                                    alt="{{ auth()->user()->name }}" class="rounded-3 shadow-sm"
                                                    style="width: 70px; height: 70px; object-fit: cover;">
                                            @else
                                                <div class="rounded-3 bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-sm"
                                                    style="width: 70px; height: 70px; font-size: 18px;">
                                                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                                </div>
                                            @endif
                                        </div>

                                        <div>
                                            <h6 class="avatar-name mb-1 fw-bold text-dark">{{ auth()->user()->name }}</h6>
                                            <p class="avatar-email text-muted mb-0 small">{{ auth()->user()->email }}</p>
                                        </div>
                                    </div>

                                    {{-- Buttons Group --}}
                                    <div
                                        class="d-flex align-items-center gap-2 w-100 w-md-auto justify-content-center justify-content-md-end flex-wrap">
                                        <button type="button"
                                            class="btn btn-sm btn-white border px-3 py-2 rounded-3 shadow-sm bg-white"
                                            id="changePhotoTextBtn">
                                            <i class="fa-solid fa-camera me-1" style="font-size:.75rem;"></i>
                                            Change Photo
                                        </button>
                                        <input type="file" name="image" id="profileImageInput" class="d-none"
                                            accept="image/*">

                                        <button type="button"
                                            class="btn btn-sm btn-outline-danger px-3 py-2 rounded-3 shadow-sm"
                                            id="changePasswordTextBtn" data-bs-toggle="modal"
                                            data-bs-target="#changePasswordModal">
                                            <i class="fa-solid fa-lock me-1" style="font-size:.75rem;"></i>
                                            Change Password
                                        </button>
                                    </div>

                                </div>
                            </div>


                            <!-- Form grid -->
                            <div class="row g-3 mb-3">
                                <div class="col-12 col-sm-6">
                                    <label class="field-label">Full Name <span class="text-danger">*</span></label>
                                    <input class="field-input" type="text" name="name" required
                                        value="{{ old('name', auth()->user()->name) }}">
                                </div>

                                <div class="col-12 col-sm-6">
                                    <label class="field-label">Email <span class="text-danger">*</span></label>
                                    <input class="field-input" type="email" name="email" required
                                        value="{{ old('email', auth()->user()->email) }}">
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-12 col-sm-6">
                                    <label class="field-label">Phone <span class="text-danger">*</span></label>
                                    <input class="field-input" type="text" name="phone" required
                                        value="{{ old('phone', $member->phone ?? '') }}">
                                </div>

                                <div class="col-12 col-sm-6">
                                    <label class="field-label">Birth Date</label>
                                    <input class="field-input js-date-picker" type="text" name="birth_date"
                                        value="{{ old('birth_date', $member->birth_date ?? '') }}">
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-12 col-sm-6">
                                    <label class="field-label">Gender <span class="text-danger">*</span></label>
                                    <select class="field-input" name="gender" required>
                                        <option value="">Select gender</option>
                                        <option value="male"
                                            {{ old('gender', $member->gender ?? '') === 'male' ? 'selected' : '' }}>Male
                                        </option>
                                        <option value="female"
                                            {{ old('gender', $member->gender ?? '') === 'female' ? 'selected' : '' }}>
                                            Female
                                        </option>
                                    </select>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <label class="field-label">Blood Group</label>
                                    <select name="blood_group" class="field-input">
                                        <option value="">Select Blood Group</option>
                                        @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                            <option value="{{ $bg }}"
                                                {{ old('blood_group', $member->blood_group ?? '') == $bg ? 'selected' : '' }}>
                                                {{ $bg }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-12 col-sm-6">
                                    <label class="field-label">Profession</label>
                                    <input class="field-input" type="text" name="profession"
                                        value="{{ old('profession', $member->profession ?? '') }}">
                                </div>

                                <div class="col-12 col-sm-6">
                                    <label class="field-label">Education</label>
                                    <input class="field-input" type="text" name="education"
                                        value="{{ old('education', $member->education ?? '') }}">
                                </div>
                            </div>

                            {{-- <div class="row g-3 mb-3">
                            <div class="col-12">
                                <label class="field-label">Marital Status</label>
                                <select class="field-input" name="marital_status" id="maritalStatus">
                                    <option value="">Select status</option>
                                    <option value="single" {{ old('marital_status', $member->marital_status ?? '') === 'single' ? 'selected' : '' }}>Single</option>
                                    <option value="married" {{ old('marital_status', $member->marital_status ?? '') === 'married' ? 'selected' : '' }}>Married</option>
                                    <option value="divorced" {{ old('marital_status', $member->marital_status ?? '') === 'divorced' ? 'selected' : '' }}>Divorced</option>
                                    <option value="widowed" {{ old('marital_status', $member->marital_status ?? '') === 'widowed' ? 'selected' : '' }}>Widowed</option>
                                </select>
                            </div>
                        </div> --}}

                            {{-- <div class="row g-3 mb-3">
                            <div class="col-12 col-sm-6">
                                <label class="field-label">Marriage Date</label>
                                <input class="field-input js-date-picker" type="text" name="marriage_date"
                                    value="{{ old('marriage_date', $member->marriage_date ?? '') }}">
                            </div>

                            <div class="col-12 col-sm-6">
                                <label class="field-label">Divorced Date</label>
                                <input class="field-input js-date-picker" type="text" name="divorced_date"
                                    value="{{ old('divorced_date', $member->divorced_date ?? '') }}">
                            </div>
                        </div> --}}

                            <div class="row g-3 mb-3">
                                <div class="col-12">
                                    <label class="field-label">Address</label>
                                    <textarea class="field-input" name="address" rows="3">{{ old('address', $member->address ?? '') }}</textarea>
                                </div>

                                <div class="col-12">
                                    <label class="field-label">Bio</label>
                                    <textarea class="field-input" name="bio" rows="4">{{ old('bio', $member->bio ?? '') }}</textarea>
                                </div>
                            </div>

                            <!-- Save button -->
                            <button type="submit" class="btn-save">
                                <i class="fa-solid fa-floppy-disk" style="font-size:.85rem;"></i>
                                Save Changes
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </main>


    {{-- chenage password --}}
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="p-4">
                    <form action="{{ route('user.password.update') }}" method="POST">
                        @csrf

                        <div class="row g-3 mb-3">
                            <div class="col-12 position-relative">
                                <label class="field-label">New Password</label>
                                <input class="field-input toggle-password" type="password" name="password"
                                    id="password" placeholder="Enter new password">
                                <i class="fa-solid fa-eye toggle-icon" onclick="togglePassword('password', this)"
                                    style="position:absolute; right:20px; top:38px; cursor:pointer;"></i>
                            </div>

                            <div class="col-12 position-relative mt-3">
                                <label class="field-label">Confirm Password</label>
                                <input class="field-input toggle-password" type="password" name="password_confirmation"
                                    id="password_confirmation" placeholder="Confirm password">
                                <i class="fa-solid fa-eye toggle-icon"
                                    onclick="togglePassword('password_confirmation', this)"
                                    style="position:absolute; right:20px; top:38px; cursor:pointer;"></i>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn-save">
                                <i class="fa-solid fa-key" style="font-size:.85rem;"></i>
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.flatpickr) {
                flatpickr('.js-date-picker', {
                    dateFormat: 'Y-m-d',
                    allowInput: true,
                    altInput: true,
                    altFormat: 'F j, Y'
                });
            }

            // const input = document.getElementById('profileImageInput');
            // const buttons = [
            //     document.getElementById('changePhotoBtn'),
            //     document.getElementById('changePhotoTextBtn'),
            // ];

            // buttons.forEach((button) => {
            //     if (button) {
            //         button.addEventListener('click', function() {
            //             input.click();
            //         });
            //     }
            // });
        });


        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);
            if (input.type === "password") {
                input.type = "text";
                icon.classList.replace("fa-eye", "fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.replace("fa-eye-slash", "fa-eye");
            }
        }
    </script>


    <script>
        const input = document.getElementById('profileImageInput');
        const previewContainer = document.getElementById('avatarPreviewContainer');
        const buttons = [
            document.getElementById('changePhotoBtn'),
            document.getElementById('changePhotoTextBtn'),
        ];

        buttons.forEach((button) => {
            if (button) {
                button.addEventListener('click', function() {
                    input.click();
                });
            }
        });

        if (input) {
            input.addEventListener('change', function(event) {
                const file = event.target.files[0];

                if (file) {
                    const previewUrl = URL.createObjectURL(file);

                    previewContainer.innerHTML = `
                <img src="${previewUrl}" alt="Preview" class="rounded-3 shadow-sm" style="width: 70px; height: 70px; object-fit: cover;">
            `;
                }
            });
        }
    </script>
@endpush
