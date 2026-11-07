@extends('layouts.user')
@section('title', 'Members - Family Feed')
@section('body_class', 'members-page')
@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .flatpickr-input[readonly] {
            background-color: #fff;
            cursor: pointer;
        }
    </style>
@endpush
@section('content')
    <main class="main-content">
        <div style="max-width:1200px;margin:0 auto;">
            <!-- Feed col -->
            <div class="row g-3 g-lg-4 justify-content-center">
                <div class="col-lg-12">
                    <!-- Page header -->
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4" data-aos="fade-right"
                        data-aos-duration="400">
                        <div>
                            <h1 class="page-title">
                                <i class="fa-solid fa-users fa-lg"></i>
                                {{ $pageTitle ?? 'Add Family Member' }}
                            </h1>
                            @if (!empty($pageHint))
                                <p class="text-muted mb-0 mt-2">{{ $pageHint }}</p>
                            @endif
                        </div>
                        <div class="">
                            <a href="{{ route('user.members') }}" class="btn-add-member text-decoration-none">Back</a>
                        </div>
                    </div>
                    <div class="">

                        <form action="{{ route('user.member.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="relation_type" value="{{ $relationType ?? '' }}">
                            <input type="hidden" name="related_member_id" value="{{ $relatedMember->id ?? '' }}">

                            {{-- ================= ACCOUNT ================= --}}
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0">Account Information</h5>
                                </div>

                                <div class="card-body">
                                    <div class="row g-3">

                                        <div class="col-md-6">
                                            <label class="form-label">Full Name *</label>
                                            <input type="text" name="name" class="form-control shadow-none"
                                                placeholder="Enter full name" value="{{ old('name') }}" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Email *</label>
                                            <input type="email" name="email" class="form-control shadow-none"
                                                placeholder="Enter email address" value="{{ old('email') }}" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Password *</label>
                                            <input type="password" name="password" class="form-control shadow-none"
                                                placeholder="Enter password" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Phone</label>
                                            <input type="text" name="phone" class="form-control shadow-none"
                                                placeholder="Enter phone number" value="{{ old('phone') }}">
                                        </div>

                                    </div>
                                </div>
                            </div>

                            {{-- ================= PERSONAL ================= --}}
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0">Personal Information</h5>
                                </div>

                                <div class="card-body">
                                    <div class="row g-3">

                                        @php
                                            $relationType = request('relation_type');
                                        @endphp

                                        <div class="col-md-4">
                                            <label class="form-label">Gender *</label>
                                            <select name="gender" class="form-select form-control shadow-none" required>
                                                <option value="">Select Gender</option>

                                                @if ($relationType !== 'mother')
                                                    <option value="male"
                                                        {{ old('gender', $prefillGender ?? '') == 'male' ? 'selected' : '' }}>
                                                        Male
                                                    </option>
                                                @endif

                                                @if ($relationType !== 'father')
                                                    <option value="female"
                                                        {{ old('gender', $prefillGender ?? '') == 'female' ? 'selected' : '' }}>
                                                        Female
                                                    </option>
                                                @endif
                                            </select>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Date of Birth</label>
                                            <input type="text" name="dob" class="form-control js-date-picker"
                                                placeholder="Select date of birth" value="{{ old('dob') }}">
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Blood Group</label>
                                            <select name="blood_group" class="form-select form-control shadow-none">
                                                <option value="">Select Blood Group</option>
                                                @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                                    <option value="{{ $bg }}"
                                                        {{ old('blood_group') == $bg ? 'selected' : '' }}>
                                                        {{ $bg }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Profession</label>
                                            <input type="text" name="profession" class="form-control shadow-none"
                                                placeholder="Enter profession" value="{{ old('profession') }}">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Education</label>
                                            <input type="text" name="education" class="form-control shadow-none"
                                                placeholder="Enter education" value="{{ old('education') }}">
                                        </div>

                                    </div>
                                </div>
                            </div>

                            {{-- ================= STATUS ================= --}}
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0">Status</h5>
                                </div>

                                <div class="card-body">
                                    <div class="row g-3">

                                        <div class="col-md-4">
                                            <label class="form-label">Living Status</label>
                                            <select name="living_status" class="form-select form-control shadow-none"
                                                id="livingStatus">
                                                <option value="alive"
                                                    {{ old('living_status') == 'alive' ? 'selected' : '' }}>
                                                    Alive</option>
                                                <option value="dead"
                                                    {{ old('living_status') == 'dead' ? 'selected' : '' }}>
                                                    Passed Away</option>
                                            </select>
                                        </div>

                                        <div class="col-md-4" id="deathDateWrapper" style="display:none;">
                                            <label class="form-label">Date of Death</label>
                                            <input type="text" name="date_of_death"
                                                class="form-control js-date-picker" value="{{ old('date_of_death') }}">
                                        </div>

                                        @php
                                            $relationType = request('relation_type');
                                        @endphp

                                        <div class="col-md-4">
                                            <label class="form-label">Marital Status</label>
                                            <select name="marital_status" class="form-select form-control shadow-none"
                                                id="maritalStatus">
                                                <option value="">Select</option>

                                                {{-- Single: Hide for Father, Mother & Spouse --}}
                                                @if (!in_array($relationType, ['father', 'mother', 'spouse']))
                                                    <option value="single"
                                                        {{ old('marital_status') == 'single' ? 'selected' : '' }}>
                                                        Single
                                                    </option>
                                                @endif

                                                <option value="married"
                                                    {{ old('marital_status', $prefillMaritalStatus ?? '') == 'married' ? 'selected' : '' }}>
                                                    Married
                                                </option>

                                                <option value="divorced"
                                                    {{ old('marital_status') == 'divorced' ? 'selected' : '' }}>
                                                    Divorced
                                                </option>

                                                {{-- Widowed: Show only if NOT Father --}}
                                                @if ($relationType !== 'father')
                                                    <option value="widowed"
                                                        {{ old('marital_status') == 'widowed' ? 'selected' : '' }}>
                                                        Widowed
                                                    </option>
                                                @endif
                                            </select>
                                        </div>

                                        {{-- Marriage Date --}}
                                        <div class="col-md-4" id="marriageDateWrapper" style="display:none;">
                                            <label class="form-label">Marriage Date</label>
                                            <input type="text" name="marriage_date"
                                                class="form-control js-date-picker" placeholder="Select Marriage Date"
                                                value="{{ old('marriage_date') }}">
                                        </div>

                                        <div class="col-md-4" id="divorceDateWrapper" style="display:none;">
                                            <label class="form-label">Divorced Date</label>
                                            <input type="text" name="divorced_date"
                                                class="form-control js-date-picker" placeholder="Select Divorced Date"
                                                value="{{ old('divorced_date') }}">
                                        </div>

                                    </div>
                                </div>
                            </div>

                            {{-- ================= ADDRESS ================= --}}
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0">Address & Bio</h5>
                                </div>

                                <div class="card-body">

                                    {{-- Address --}}
                                    <div class="mb-3">
                                        <label class="form-label">Address</label>
                                        <textarea name="address" class="form-control shadow-none" rows="2" placeholder="Enter your address">{{ old('address') }}</textarea>
                                    </div>

                                    {{-- Bio --}}
                                    <div class="mb-3">
                                        <label class="form-label">Bio</label>
                                        <textarea name="bio" class="form-control shadow-none" rows="4"
                                            placeholder="Write a short bio about yourself">{{ old('bio') }}</textarea>
                                    </div>

                                </div>
                            </div>

                            {{-- ================= IMAGES ================= --}}
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0">Images</h5>
                                </div>

                                <div class="card-body">
                                    <div class="row g-4">
                                        {{-- <div class="col-md-4">
                                            <label class="form-label">Profile Photo</label>
                                            <div class="text-center">
                                                <input type="file" name="profile_image"
                                                    class="form-control shadow-none" accept="image/*"
                                                    onchange="previewProfile(this)">
                                                <img id="profilePreview" src="https://via.placeholder.com/150"
                                                    class="rounded-circle border mt-2" width="150" height="150"
                                                    style="object-fit: cover;">
                                            </div>
                                        </div> --}}
                                        <div class="col-md-12">
                                            <label class="form-label">Profile Photo</label>
                                            <input type="file" name="profile_image" class="dropify" data-height="150"
                                                accept="image/*"
                                                data-default-file="{{ old('profile_image') }}">
                                        </div>

                                        {{-- <div class="col-md-8">
                                            <label class="form-label">Gallery Images</label>
                                            <input type="file" id="galleryInput" name="gallery[]"
                                                class="form-control mb-3" multiple accept="image/*"
                                                onchange="previewGallery(this)">
                                            <div id="galleryPreview" class="row g-2"></div>
                                        </div> --}}

                                    </div>
                                </div>
                            </div>

                            {{-- ================= BUTTONS ================= --}}
                            <div class="text-end">
                                <button type="reset" class="btn btn-light">
                                    Cancel
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    Save Member
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="createFamilyModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('user.storeFamily') }}" class="modal-content">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Create Family</h5>
                </div>

                <div class="modal-body">
                    <label>Family Name</label>
                    <input type="text" name="name" class="form-control shadow-none" required>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        Create Family
                    </button>
                </div>

            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    @if (!$family)
        <script>
            window.onload = function() {
                var myModal = new bootstrap.Modal(document.getElementById('createFamilyModal'));
                myModal.show();
            }
        </script>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr('.js-date-picker', {
                dateFormat: 'Y-m-d',
                allowInput: true,
                altInput: true,
                altFormat: 'F j, Y'
            });

            const livingStatus = document.getElementById('livingStatus');
            const deathDateWrapper = document.getElementById('deathDateWrapper');
            const maritalStatus = document.getElementById('maritalStatus');
            const marriageDateWrapper = document.getElementById('marriageDateWrapper');
            const divorceDateWrapper = document.getElementById('divorceDateWrapper');

            function toggleLiving() {
                if (!livingStatus || !deathDateWrapper) return;
                deathDateWrapper.style.display = livingStatus.value === 'dead' ? 'block' : 'none';
            }

            function toggleMarital() {
                if (!maritalStatus) return;

                const status = maritalStatus.value;

                if (marriageDateWrapper) {
                    marriageDateWrapper.style.display = (status === 'married' || status === 'divorced') ? 'block' :
                        'none';
                }

                if (divorceDateWrapper) {
                    divorceDateWrapper.style.display = status === 'divorced' ? 'block' : 'none';
                }
            }

            livingStatus?.addEventListener('change', toggleLiving);
            maritalStatus?.addEventListener('change', toggleMarital);

            toggleLiving();
            toggleMarital();
        });

        function previewProfile(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    document.getElementById('profilePreview').src = e.target.result;
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    <script>
        function previewGallery(input) {
            let preview = document.getElementById('galleryPreview');
            preview.innerHTML = "";

            if (input.files) {
                Array.from(input.files).forEach(file => {
                    let reader = new FileReader();

                    reader.onload = function(e) {
                        let col = document.createElement("div");
                        col.className = "col-4 col-md-3";

                        col.innerHTML = `
                            <div class="border rounded overflow-hidden">
                                <img src="${e.target.result}"
                                    class="w-100"
                                    style="height: 120px; object-fit: cover;">
                            </div>
                        `;

                        preview.appendChild(col);
                    };

                    reader.readAsDataURL(file);
                });
            }
        }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.dropify').dropify({
                messages: {
                    'default': 'Drag and drop',
                    'replace': 'Drag and drop',
                    'remove': 'Remove',
                    'error': 'Ooops, something wrong happened.'
                },
            });
        });
    </script>
@endpush
