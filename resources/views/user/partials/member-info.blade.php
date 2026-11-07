@php
    // $image = $member->user?->image;
    // $imagePath = $image ? public_path($image) : null;

    // if ($image && file_exists($imagePath)) {
    //     $profileImage = asset($image);
    // } else {
    //     $profileImage = $member->gender === 'female' ? asset('assets/female.jpg') : asset('assets/male.jpg');
    // }
    $userImage = $member->user?->image;
    $defaultImage = strtolower($member->gender) === 'female' 
        ? asset('assets/female.jpg') 
        : asset('assets/male.jpg');

    $profileImage = $userImage ? getPhoto($userImage) : $defaultImage;

    $displayName = $member->user?->name ?? 'Unknown Member';
    $profession = $member->profession ?? '-';
    $gender = $member->gender ? ucfirst($member->gender) : '-';
    $livingStatus = ucfirst($member->living_status ?? 'unknown');
    $maritalStatus = $member->marital_status ? ucfirst($member->marital_status) : '-';
    $birthDate = $member->birth_date ? date('M d, Y', strtotime($member->birth_date)) : '-';
    $deathDate = $member->date_of_death ? date('M d, Y', strtotime($member->date_of_death)) : '-';
    $marriageDate = $member->marriage_date ? date('M d, Y', strtotime($member->marriage_date)) : '-';
    $divorcedDate = $member->divorced_date ? date('M d, Y', strtotime($member->divorced_date)) : '-';
@endphp

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-white border-0 p-0">
        <div class="px-3 px-sm-4 py-3 py-sm-4"
            style="background: linear-gradient(135deg, rgba(15,23,42,.04), rgba(152,75,243,.06));">
            <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap flex-sm-nowrap">

                <div class="d-flex align-items-center gap-3 min-w-0">
                    <img src="{{ $profileImage }}" alt="{{ $displayName }}"
                        class="rounded-circle flex-shrink-0 border border-3 border-white shadow-sm"
                        style="width: 72px; height: 72px; object-fit: cover;">

                    <div class="min-w-0">
                        <h5 class="mb-1 fw-bold text-truncate" style="font-size: 1rem;">{{ $displayName }}</h5>
                        <div class="text-muted small text-truncate">{{ $profession }}</div>
                        <div class="d-flex flex-wrap gap-1 mt-2">
                            <span class="badge rounded-pill text-bg-light border"
                                style="font-size: 11px;">{{ $gender }}</span>
                            <span class="badge rounded-pill text-bg-light border"
                                style="font-size: 11px;">{{ $livingStatus }}</span>
                            @if ($member->marital_status)
                                <span class="badge rounded-pill text-bg-light border"
                                    style="font-size: 11px;">{{ $maritalStatus }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Button Fixed --}}
                <div class="flex-shrink-0 mt-2 mt-sm-0">
                    <a href="{{ route('user.member.profile', $member->user->id) }}"
                        class="btn btn-sm rounded-3 px-2.5 py-1.5 fw-medium shadow-sm custom-btn-primary d-inline-flex align-items-center gap-1"
                        style="font-size: 0.8rem; white-space: nowrap;">
                        <i class="fa-solid fa-user" style="font-size: 0.75rem;"></i> View Profile
                    </a>
                </div>

            </div>
        </div>
    </div>

    <div class="card-body p-4">
        <div class="row g-4">
            <div class="col-12 col-lg-12">
                <div class="mb-4">
                    <h6 class="fw-semibold mb-3 text-uppercase small text-muted">Personal Information</h6>

                    <div class="list-group list-group-flush border rounded-3 overflow-hidden">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-muted">Full Name</span>
                            <span class="fw-medium text-end">{{ $displayName }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-muted">Email</span>
                            <span class="fw-medium text-end">{{ $member->user?->email ?? '-' }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-muted">Phone</span>
                            <span class="fw-medium text-end">{{ $member->phone ?? '-' }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-muted">Gender</span>
                            <span class="fw-medium text-end">{{ $gender }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-muted">Date of Birth</span>
                            <span class="fw-medium text-end">{{ $birthDate }}</span>
                        </div>
                        @if ($member->living_status == 'dead')
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="text-muted">Date of Death</span>
                                <span class="fw-medium text-end">{{ $deathDate }}</span>
                            </div>
                        @endif
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-muted">Blood Group</span>
                            <span class="fw-medium text-end">{{ $member->blood_group ?? '-' }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-muted">Profession</span>
                            <span class="fw-medium text-end">{{ $profession }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-muted">Education</span>
                            <span class="fw-medium text-end">{{ $member->education ?? '-' }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-muted">Address</span>
                            <span class="fw-medium text-end">{{ $member->address ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-12">
                <div class="mb-4">
                    <h6 class="fw-semibold mb-3 text-uppercase small text-muted">Marriage Information</h6>

                    <div class="list-group list-group-flush border rounded-3 overflow-hidden">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-muted">Marital Status</span>
                            <span class="fw-medium text-end">{{ $maritalStatus }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-muted">Marriage Date</span>
                            <span class="fw-medium text-end">{{ $marriageDate }}</span>
                        </div>
                        @if ($member->marital_status == 'divorced')
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="text-muted">Divorced Date</span>
                                <span class="fw-medium text-end">{{ $divorcedDate }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div>
                    <h6 class="fw-semibold mb-3 text-uppercase small text-muted">Bio</h6>
                    <div class="border rounded-3 p-3 bg-light">
                        <p class="mb-0 text-muted" style="line-height: 1.75;">
                            {{ $member->bio ?? 'No bio added yet.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
