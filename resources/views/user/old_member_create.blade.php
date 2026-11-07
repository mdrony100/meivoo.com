@extends('layouts.user')
@section('title', 'Members - Family Feed')
@section('body_class', 'members-page')
@push('styles')
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
                                Add Family Member
                            </h1>
                        </div>
                        <div class="">
                            <a href="{{ route('user.members') }}" class="btn-add-member text-decoration-none">Back</a>
                        </div>
                    </div>
                    <div class="">

                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">
                                    <i class="fa-solid fa-user-plus me-2"></i>
                                    Add Family Member
                                </h5>
                            </div>

                            <div class="card-body">

                                <div class="row g-3">

                                    <!-- Account Information -->
                                    <div class="col-md-6">
                                        <label class="form-label">Full Name *</label>
                                        <input type="text" class="form-control">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Email Address</label>
                                        <input type="email" class="form-control">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Password</label>
                                        <input type="password" class="form-control">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Phone Number</label>
                                        <input type="text" class="form-control">
                                    </div>

                                    <!-- Personal Information -->
                                    <div class="col-md-4">
                                        <label class="form-label">Gender *</label>
                                        <select class="form-select">
                                            <option>Male</option>
                                            <option>Female</option>
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Date of Birth</label>
                                        <input type="date" class="form-control">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Blood Group</label>
                                        <select class="form-select">
                                            <option>A+</option>
                                            <option>A-</option>
                                            <option>B+</option>
                                            <option>B-</option>
                                            <option>AB+</option>
                                            <option>AB-</option>
                                            <option>O+</option>
                                            <option>O-</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Profession</label>
                                        <input type="text" class="form-control">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Education</label>
                                        <input type="text" class="form-control">
                                    </div>

                                    <!-- Status -->
                                    <div class="col-md-4">
                                        <label class="form-label">Living Status</label>
                                        <select class="form-select">
                                            <option>Alive</option>
                                            <option>Passed Away</option>
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Date of Death</label>
                                        <input type="date" class="form-control">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Marital Status</label>
                                        <select class="form-select">
                                            <option>Single</option>
                                            <option>Married</option>
                                            <option>Divorced</option>
                                            <option>Widowed</option>
                                        </select>
                                    </div>

                                    <!-- Address -->
                                    <div class="col-md-12">
                                        <label class="form-label">Address</label>
                                        <textarea class="form-control" rows="2"></textarea>
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label">Biography / About</label>
                                        <textarea class="form-control" rows="4"></textarea>
                                    </div>

                                    <!-- Social -->
                                    <div class="col-md-4">
                                        <label class="form-label">Facebook Profile</label>
                                        <input type="url" class="form-control">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Instagram</label>
                                        <input type="url" class="form-control">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Website</label>
                                        <input type="url" class="form-control">
                                    </div>

                                    <!-- Dates -->
                                    <div class="col-md-6">
                                        <label class="form-label">Marriage Date</label>
                                        <input type="date" class="form-control">
                                    </div>

                                    {{-- <div class="col-md-6">
                                        <label class="form-label">Anniversary Date</label>
                                        <input type="date" class="form-control">
                                    </div> --}}

                                    <!-- Profile Image -->
                                    <div class="col-md-12">
                                        <label class="form-label">Profile Photo</label>
                                        <input type="file" class="dropify">
                                    </div>

                                    <!-- Gallery -->
                                    <div class="col-md-12">
                                        <label class="form-label">Gallery Images</label>

                                        <div class="input-images"></div>

                                        <!-- jQuery Image Uploader -->
                                    </div>

                                    <!-- Notes -->
                                    <div class="col-md-12">
                                        <label class="form-label">Notes</label>
                                        <textarea class="form-control" rows="3"></textarea>
                                    </div>

                                </div>

                                <hr>

                                <div class="text-end">
                                    <button class="btn btn-light">
                                        Cancel
                                    </button>

                                    <button class="btn btn-primary">
                                        <i class="fa-solid fa-floppy-disk me-1"></i>
                                        Save Member
                                    </button>
                                </div>

                            </div>
                        </div>

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
                    <input type="text" name="name" class="form-control" required>
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
    @if(!$family)
        <script>
            window.onload = function () {
                var myModal = new bootstrap.Modal(document.getElementById('createFamilyModal'));
                myModal.show();
            }
        </script>
    @endif
@endpush
