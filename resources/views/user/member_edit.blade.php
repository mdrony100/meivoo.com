@extends('layouts.user')
@section('title', 'Edit Family Member - Family Feed')
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
    @php
        $memberUser = $member->user;
        $memberName = old('name', $memberUser->name ?? ($member->full_name ?? ''));
        $memberEmail = old('email', $memberUser->email ?? '');
        $memberPhone = old('phone', $member->phone ?? '');
        $memberGender = old('gender', $member->gender ?? '');
        $memberDob = old('dob', $member->birth_date ?? '');
        $memberBloodGroup = old('blood_group', $member->blood_group ?? '');
        $memberProfession = old('profession', $member->profession ?? ($member->occupation ?? ''));
        $memberEducation = old('education', $member->education ?? '');
        $memberLivingStatus = old(
            'living_status',
            $member->living_status ?? (($member->status ?? 'alive') === 'passed' ? 'dead' : 'alive'),
        );
        $memberDateOfDeath = old('date_of_death', $member->date_of_death ?? ($member->death_date ?? ''));
        $memberMaritalStatus = old('marital_status', $member->marital_status ?? '');
        $memberMarriageDate = old('marriage_date', $member->marriage_date ?? '');
        $memberDivorcedDate = old('divorced_date', $member->divorced_date ?? '');
        $memberAddress = old('address', $member->address ?? '');
        $memberBio = old('bio', $member->bio ?? '');
        $memberImage = $memberUser?->image ? asset($memberUser->image) : 'https://via.placeholder.com/150';
        $showDeathDate = $memberLivingStatus === 'dead';
        $showMarriageDate = in_array($memberMaritalStatus, ['married', 'divorced']);
        $showDivorceDate = $memberMaritalStatus === 'divorced';
    @endphp

    <main class="main-content">
        <div style="max-width:1200px;margin:0 auto;">
            <div class="row g-3 g-lg-4 justify-content-center">
                <div class="col-lg-12">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4" data-aos="fade-right"
                        data-aos-duration="400">
                        <div>
                            <h1 class="page-title">
                                <i class="fa-solid fa-user-pen fa-lg"></i>
                                Edit Family Member
                            </h1>
                        </div>
                        <div>
                            <a href="{{ route('user.members') }}" class="btn-add-member text-decoration-none">Back</a>
                        </div>
                    </div>

                    <form action="{{ route('user.member.update', $member->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Account Information</h5>
                            </div>

                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Full Name *</label>
                                        <input type="text" name="name" class="form-control shadow-none"
                                            value="{{ $memberName }}" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control shadow-none"
                                            value="{{ $memberEmail }}" required>
                                    </div>

                                    {{-- <div class="col-md-6">
                                        <label class="form-label">Password</label>
                                        <input type="password" name="password" class="form-control shadow-none"
                                            placeholder="Leave blank to keep current password">
                                    </div> --}}

                                    <div class="col-md-6">
                                        <label class="form-label">Phone</label>
                                        <input type="text" name="phone" class="form-control shadow-none"
                                            value="{{ $memberPhone }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Personal Information</h5>
                            </div>

                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Gender *</label>
                                        <select name="gender" class="form-select shadow-none form-control" required>
                                            <option value="">Select</option>
                                            <option value="male" {{ $memberGender === 'male' ? 'selected' : '' }}>Male
                                            </option>
                                            <option value="female" {{ $memberGender === 'female' ? 'selected' : '' }}>Female
                                            </option>
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Date of Birth</label>
                                        <input type="text" name="dob" class="form-control js-date-picker shadow-none"
                                            value="{{ $memberDob }}">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Blood Group</label>
                                        <select name="blood_group" class="form-select shadow-none form-control">
                                            <option value="">Select</option>
                                            @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                                <option value="{{ $bg }}"
                                                    {{ $memberBloodGroup === $bg ? 'selected' : '' }}>
                                                    {{ $bg }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Profession</label>
                                        <input type="text" name="profession" class="form-control shadow-none"
                                            value="{{ $memberProfession }}">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Education</label>
                                        <input type="text" name="education" class="form-control shadow-none"
                                            value="{{ $memberEducation }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Status</h5>
                            </div>

                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Living Status</label>
                                        <select name="living_status" class="form-select shadow-none form-control"
                                            id="livingStatus">
                                            <option value="alive" {{ $memberLivingStatus === 'alive' ? 'selected' : '' }}>
                                                Alive</option>
                                            <option value="dead" {{ $memberLivingStatus === 'dead' ? 'selected' : '' }}>
                                                Passed Away</option>
                                        </select>
                                    </div>

                                    <div class="col-md-4" id="deathDateWrapper"
                                        style="display: {{ $showDeathDate ? 'block' : 'none' }};">
                                        <label class="form-label">Date of Death</label>
                                        <input type="text" name="date_of_death"
                                            class="form-control js-date-picker shadow-none"
                                            value="{{ $memberDateOfDeath }}">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Marital Status</label>
                                        <select name="marital_status" class="form-select shadow-none form-control"
                                            id="maritalStatus">
                                            <option value="">Select</option>
                                            <option value="single"
                                                {{ $memberMaritalStatus === 'single' ? 'selected' : '' }}>Single</option>
                                            <option value="married"
                                                {{ $memberMaritalStatus === 'married' ? 'selected' : '' }}>Married</option>
                                            <option value="divorced"
                                                {{ $memberMaritalStatus === 'divorced' ? 'selected' : '' }}>Divorced
                                            </option>
                                            <option value="widowed"
                                                {{ $memberMaritalStatus === 'widowed' ? 'selected' : '' }}>Widowed</option>
                                        </select>
                                    </div>

                                    <div class="col-md-4" id="marriageDateWrapper"
                                        style="display: {{ $showMarriageDate ? 'block' : 'none' }};">
                                        <label class="form-label">Marriage Date</label>
                                        <input type="text" name="marriage_date"
                                            class="form-control js-date-picker shadow-none"
                                            value="{{ $memberMarriageDate }}">
                                    </div>

                                    <div class="col-md-4" id="divorceDateWrapper"
                                        style="display: {{ $showDivorceDate ? 'block' : 'none' }};">
                                        <label class="form-label">Divorced Date</label>
                                        <input type="text" name="divorced_date"
                                            class="form-control js-date-picker shadow-none"
                                            value="{{ $memberDivorcedDate }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Address & Bio</h5>
                            </div>

                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Address</label>
                                    <textarea name="address" class="form-control shadow-none" rows="2">{{ $memberAddress }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Bio</label>
                                    <textarea name="bio" class="form-control shadow-none" rows="4">{{ $memberBio }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Images</h5>
                            </div>

                            <div class="card-body">
                                <div class="row g-4">
                                    {{-- <div class="col-md-4">
                                        <label class="form-label">Profile Photo</label>
                                        <div class="text-center">
                                            <input type="file" name="profile_image" class="form-control shadow-none" accept="image/*"
                                                onchange="previewProfile(this)">
                                            <img id="profilePreview"
                                                src="{{ $memberImage }}"
                                                class="rounded-circle border mt-2"
                                                width="150"
                                                height="150"
                                                style="object-fit: cover;">
                                        </div>
                                    </div> --}}
                                    <div class="col-md-12">
                                        <label class="form-label">Profile Photo</label>
                                        <div class="text-center">
                                            <input type="file" name="profile_image" class="dropify" data-height="150"
                                                data-default-file="{{ $memberImage }}" accept="image/*">
                                        </div>
                                    </div>

                                    {{-- <div class="col-md-7">
                                        <label class="form-label">Gallery Images</label>
                                        <input type="file" id="galleryInput" name="gallery[]"
                                            class="form-control mb-3" multiple accept="image/*"
                                            onchange="previewGallery(this)">

                                        @if (($galleryImages ?? collect())->isNotEmpty())
                                            <div class="mb-3">
                                                <div class="row g-2">
                                                    @foreach ($galleryImages as $galleryImage)
                                                        @php
                                                            $galleryPath =
                                                                $galleryImage->image ??
                                                                ($galleryImage->image_path ?? null);
                                                        @endphp
                                                        @if ($galleryPath)
                                                            <div class="col-4 col-md-3">
                                                                <div
                                                                    class="border rounded overflow-hidden position-relative">
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-danger rounded-circle position-absolute top-0 end-0 m-1 z-3"
                                                                        style="width: 30px; height: 30px; padding: 0;"
                                                                        data-delete-url="{{ route('user.member.gallery.destroy', $galleryImage->id) }}"
                                                                        onclick="deleteGalleryImage(this)">
                                                                        <i class="fa-solid fa-xmark"></i>
                                                                    </button>
                                                                    <img src="{{ asset($galleryPath) }}" class="w-100"
                                                                        style="height: 120px; object-fit: cover;">
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        <div id="galleryPreview" class="row g-2"></div>
                                    </div> --}}
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <a href="{{ route('user.members') }}" class="btn btn-light">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Member</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
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

        function deleteGalleryImage(button) {
            if (!confirm('Remove this gallery image?')) {
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = button.dataset.deleteUrl;

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = @json(csrf_token());

            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';

            form.appendChild(csrf);
            form.appendChild(method);
            document.body.appendChild(form);
            form.submit();
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
