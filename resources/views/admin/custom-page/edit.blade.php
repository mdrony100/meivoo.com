@extends('admin.layouts.app')
@section('cpage', 'active')
@section('title') {{ $data['title'] ?? '' }} @endsection
@push('style')
    <style>
        .hr-text {
            display: flex;
            align-items: center;
            margin: 2rem 0;
            font-size: .825rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .04em;
            line-height: 1rem;
            color: #6c7a91;
            height: 1px;
        }

        .hr-text:before {
            content: "";
            margin-right: .5rem;
        }

        .hr-text:after,
        .hr-text:before {
            flex: 1 1 auto;
            height: 1px;
            background-color: #dce1e7;
        }

        .hr-text:after {
            content: "";
            margin-left: .5rem;
        }
    </style>
@endpush
@php
    $row = $data['row'];
@endphp
@section('content')
    <div class="content-wrapper">

        <div class="content">
            <div class="">
                <div class="row">
                    <div class="mb-3 col-12">
                        <div class="card content-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h2 class="card-title mb-0">Manage {{ $data['title'] ?? '' }}</h2>
                                <a href="{{ route('admin.cpage.index') }}" class="btn btn-primary btn-gradient btn-sm">
                                    Back
                                </a>
                            </div>

                            <div class="card-body table-responsive p-4">
                                <form action="{{ route('admin.cpage.update', $row->id) }}" method="post"
                                    enctype="multipart/form-data">
                                    @csrf

                                    <div class="row">

                                        <!-- Title -->
                                        <div class="mb-3 col-12">
                                            <div class="form-group">
                                                <label class="form-label" for="title">Page Name</label>
                                                <input type="text" name="title" id="title" class="form-control"
                                                    required value="{{ $row->title }}" placeholder="Page Name">
                                            </div>
                                        </div>


                                        <div class="mb-3 col-12">
                                            <div class="form-group">
                                                <label class="form-label">Description <span
                                                        class="text-danger">*</span></label>
                                                <textarea name="body" cols="30" rows="5" class="form-control summernote" required>{!! $row->body !!}</textarea>
                                            </div>
                                        </div>

                                        <div class="mb-3 col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="meta_title">Meta Title <span
                                                        class="text-info">(Recommended)</span></label>
                                                <input type="text" name="meta_title" id="meta_title" class="form-control"
                                                    value="{{ $row->meta_title }}" required>
                                            </div>
                                        </div>

                                        <div class="mb-3 col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="meta_keywords">Meta Keywords</label>
                                                <input type="text" name="meta_keywords" id="meta_keywords"
                                                    class="form-control" value="{{ $row->meta_keywords }}" required>
                                            </div>
                                        </div>

                                        <div class="mb-3 col-12">
                                            <div class="form-group">
                                                <label class="form-label" for="meta_description">Meta Description <span
                                                        class="text-info">(Recommended)</span></label>
                                                <textarea name="meta_description" cols="30" rows="5" id="meta_description" class="form-control">{{ $row->meta_description }}</textarea>
                                            </div>
                                        </div>

                                        <!-- Submit -->
                                        <div class="mb-3 col-12">
                                            <div class="form-group text-end">
                                                <button type="submit" class="btn btn-primary">
                                                    Update
                                                </button>
                                            </div>
                                        </div>

                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.summernote').summernote({
                height: 400
            });
        });
        // slug generator
        $(document).on('input', '#title', function() {
            let titleValue = event.target.value;
            let slug = titleValue.toLowerCase()
                .replace(/[`~!@#$%^&*()_\-+=\[\]{};:'"\\|\/,.<>?\s]/g, ' ')
                .replace(/^\s+|\s+$/gm, '')
                .replace(/\s+/g, '-');
            $('#slug').val(slug);
        })
    </script>
@endpush
