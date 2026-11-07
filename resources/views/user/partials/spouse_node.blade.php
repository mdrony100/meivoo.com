<div class="person {{ $spouse->gender }}">
    {{-- <div class="position-relative">
        <a href="{{ route('user.member.profile', $spouse->user->id) }}" data-bs-toggle="tooltip" data-bs-placement="top"
            title="View Profile">
            @php
                $image = $spouse->user?->image;
                $imagePath = $image ? public_path($image) : null;
                if ($image && file_exists($imagePath)) {
                    $profileImage = asset($image);
                } else {
                    $profileImage =
                        $spouse->gender === 'female' ? asset('assets/female.jpg') : asset('assets/male.jpg');
                }
            @endphp
            <img src="{{ $profileImage }}" class="profile-photo" alt="{{ $member->name }}">
        </a>
    </div> --}}
    <div class="position-relative">
        <a href="{{ route('user.member.profile', $spouse->user?->id ?? '#') }}" data-bs-toggle="tooltip"
            data-bs-placement="top" title="View Profile">

            @php
                $spouseImage = $spouse->user?->image;
                $defaultImage =
                    strtolower($spouse->gender) === 'female' ? asset('assets/female.jpg') : asset('assets/male.jpg');
            @endphp

            <img src="{{ $spouseImage ? getPhoto($spouseImage) : $defaultImage }}" class="profile-photo"
                alt="{{ $spouse->name }}">
        </a>
    </div>
    <div class="profile-info">
        <a href="{{ route('user.family-tree', $member->id) }}" data-bs-toggle="tooltip" data-bs-placement="top"
            title="View Family">
            <span class="profile-name clickable m-0">{{ $spouse->user?->name ?? 'Unknown' }}</span>
        </a>
        @if ($spouse->birth_date)
            <span class="age-range text-bold mt-2">
                {{ $spouse->birth_date ? date('M d, Y', strtotime($spouse->birth_date)) : '' }}
            </span>
        @endif
        @if ($spouse->profession)
            <span class="profession">
                {{ $spouse->profession }}
            </span>
        @endif
    </div>
    <button class="view-btn viewFamilyMemberInfo" data-id="{{ $spouse->id }}">
        <i class="fas fa-eye"></i>
    </button>
</div>
