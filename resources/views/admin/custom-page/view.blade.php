@extends('admin.layouts.app')
@section('cpage', 'active')
@section('title') {{ $data['title'] ?? '' }} @endsection

@php
    $row = $data['row'];
@endphp

@push('style')
    <style>
        td {
            width: 0;
        }
    </style>
@endpush

@section('content')
    <div class="content-wrapper">

        <div class="content">
            <div class="">
                <div class="row">
                    <div class="col-12">
                        <div class="card content-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h2 class="card-title mb-0">Manage {{ $data['title'] ?? '' }}</h2>
                                <a href="{{ route('admin.cpage.index') }}" class="btn btn-primary btn-gradient btn-sm">
                                    Back
                                </a>
                            </div>

                            <table class="table">
                                <tr>
                                    <td style="width:10%;">Page Name :</td>
                                    <td>{{ $row->title }}</td>
                                </tr>
                                <tr>
                                    <td>Page Slug :</td>
                                    <td>{{ $row->url_slug }}</td>
                                </tr>
                                {{-- <tr>
                                    <td>Publihed Status :</td>
                                    <td>
                                        @if ($row->is_active == 1)
                                            <span class="badge badge-success">Publihed</span>
                                        @else
                                            <span class="badge badge-danger">Unpublished</span>
                                        @endif
                                    </td>
                                </tr> --}}
                                <tr>
                                    <td colspan="2">Description :
                                        <br>
                                        {!! $row->body !!}
                                    </td>
                                </tr>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
