 <li class="branch-wrapper">
     <span class="tf-nc" style="z-index:1">
         <div class="{{ $member->spouses->count() ? 'couple-card-shared group-clickable' : 'single-card' }}">
             @if (isset($member->spouses[0]))
                 @include('user.partials.spouse_node', ['spouse' => $member->spouses[0]])
                 <div class="marriage-connector-center">
                     <span data-bs-toggle="tooltip" data-bs-placement="top"
                         data-bs-title="{{ $member->spouses[0]->marital_status === 'divorced' ? 'Divorced' : 'Married' }}">
                         {!! $member->spouses[0]->marital_status === 'divorced' ? '💔' : '👩‍❤️‍👨' !!}
                     </span>
                 </div>
             @endif

             <div class="person {{ $member->gender }}">
                 {{-- <div class="position-relative main-child">
                     <a href="{{ route('user.member.profile', $member->user->id) }}" data-bs-toggle="tooltip"
                         data-bs-placement="top" title="View Profile">
                         @php
                             $image = $member->user?->image;
                             $imagePath = $image ? public_path($image) : null;
                             if ($image && file_exists($imagePath)) {
                                 $profileImage = asset($image);
                             } else {
                                 $profileImage =
                                     $member->gender === 'female'
                                         ? asset('assets/female.jpg')
                                         : asset('assets/male.jpg');
                             }
                         @endphp
                         <img src="{{ $profileImage }}" class="profile-photo" alt="{{ $member->name }}">
                     </a>
                 </div> --}}
                 <div class="position-relative main-child">
                     <a href="{{ route('user.member.profile', $member->user?->id ?? '#') }}" data-bs-toggle="tooltip"
                         data-bs-placement="top" title="View Profile">

                         @php
                             $userImage = $member->user?->image;
                             $defaultImage =
                                 strtolower($member->gender) === 'female'
                                     ? asset('assets/female.jpg')
                                     : asset('assets/male.jpg');
                         @endphp

                         <img src="{{ $userImage ? getPhoto($userImage) : $defaultImage }}" class="profile-photo"
                             alt="{{ $member->name }}">
                     </a>
                 </div>

                 <div class="profile-info">
                     <a href="{{ route('user.family-tree', $member->id) }}" data-bs-toggle="tooltip"
                         data-bs-placement="top" title="View Family">
                         <span class="profile-name clickable m-0">{{ $member->user?->name ?? '' }}</span>
                     </a>
                     @if ($member->birth_date)
                         <span class="age-range text-bold mt-2">
                             {{ $member->birth_date ? date('M d, Y', strtotime($member->birth_date)) : '' }}
                         </span>
                     @endif
                     @if ($member->profession)
                         <span class="profession">{{ $member->profession }}</span>
                     @endif
                 </div>
                 <button class="view-btn viewFamilyMemberInfo" data-id="{{ $member->id }}">
                     <i class="fas fa-eye"></i>
                 </button>
             </div>

             @if (isset($member->spouses[1]))
                 <div class="marriage-connector-center">
                     <span data-bs-toggle="tooltip" data-bs-placement="top"
                         data-bs-title="{{ $member->spouses[1]->marital_status === 'divorced' ? 'Divorced' : 'Married' }}">
                         {!! $member->spouses[1]->marital_status === 'divorced' ? '💔' : '👩‍❤️‍👨' !!}
                     </span>
                 </div>
                 @include('user.partials.spouse_node', ['spouse' => $member->spouses[1]])
             @endif

         </div>
     </span>
     @php
         $children = $member->children ?? collect();
         $maxSlots = max(2, $children->count());
         $currentDepth = $depth ?? 1;
         $maxDepth = 4;
     @endphp

     @if ($currentDepth < $maxDepth)
         <ul>
             @for ($i = 0; $i < $maxSlots; $i++)

                 @if (isset($children[$i]))
                     @include('user.partials.family_node', [
                         'member' => $children[$i],
                         'depth' => $currentDepth + 1,
                     ])
                 @else
                     <li class="branch-wrapper">
                         <span class="tf-nc">
                             <div class="single-card placeholder-card placeholder_card">
                                 <div class="person">
                                     <div class="position-relative main-child">
                                         <img src="{{ asset('assets/default.jpg') }}" class="profile-photo"
                                             alt="">
                                     </div>
                                     <div class="profile-info">
                                         <span class="profile-name">Name</span>
                                         <span class="profession">Profession</span>
                                     </div>
                                 </div>
                             </div>
                         </span>

                         @if ($currentDepth + 1 < $maxDepth)
                             <ul>
                                 @for ($j = 0; $j < $maxSlots; $j++)
                                     <li class="branch-wrapper">
                                         <span class="tf-nc">
                                             <div class="single-card placeholder-card placeholder_card">
                                                 <div class="person">
                                                     <div class="position-relative main-child">
                                                         <img src="{{ asset('assets/default.jpg') }}"
                                                             class="profile-photo" alt="">
                                                     </div>
                                                     <div class="profile-info">
                                                         <span class="profile-name">Name</span>
                                                         <span class="profession">Profession</span>
                                                     </div>
                                                 </div>
                                             </div>
                                         </span>
                                         <ul>
                                             @for ($l = 0; $l < $maxSlots; $l++)
                                                 <li class="branch-wrapper">
                                                     <span class="tf-nc">
                                                         <div class="single-card placeholder-card placeholder_card">
                                                             <div class="person">
                                                                 <div class="position-relative main-child">
                                                                     <img src="{{ asset('assets/default.jpg') }}"
                                                                         class="profile-photo" alt="">
                                                                 </div>
                                                                 <div class="profile-info">
                                                                     <span class="profile-name">Name</span>
                                                                     <span class="profession">Profession</span>
                                                                 </div>
                                                             </div>
                                                         </div>
                                                     </span>
                                                 </li>
                                             @endfor
                                         </ul>
                                     </li>
                                 @endfor
                             </ul>
                         @endif
                     </li>
                 @endif
             @endfor
         </ul>
     @endif
 </li>
