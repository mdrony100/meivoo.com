@extends('admin.layouts.app')
@section('title') {{ $data['title'] ?? '' }} @endsection
@section('cpage', 'active')

@php
    $rows = $data['rows'];
@endphp

@section('content')
    <div class="content-wrapper">

        <div class="content">
            <div class="">
                <div class="row">
                    <div class="">
                        <div class="card content-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h2 class="card-title mb-0">Manage {{ $data['title'] ?? '' }}</h2>
                            </div>

                            <div class="card-body table-responsive p-3">
                                <table id="dataTables" class="table table-striped table-hover">
                                    <thead class="table top-school-table mb-0">
                                        <tr>
                                            <th style="width:10%;">SN</th>
                                            <th style="width:50%;">Page Name</th>
                                            <th style="width:20%;" class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($rows as $key => $row)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $row->title }}</td>

                                                <td>
                                                    <div class="gap-2">
                                                        <div class="action-buttons">
                                                            <a href="{{ route('admin.cpage.view', $row->id) }}"
                                                                class="btn btn-sm btn-outline-primary btn-icon">
                                                                <i class="bi bi-eye"></i>
                                                                View
                                                            </a>
                                                            <a href="{{ route('admin.cpage.edit', $row->id) }}"
                                                                class="btn btn-sm btn-outline-secondary btn-icon">
                                                                <i class="bi bi-pencil"></i>
                                                                Edit
                                                            </a>
                                                            {{-- <a href="{{ route('admin.cpage.delete', $row->id) }}"
                                                                class="btn btn-sm btn-outline-danger btn-icon">
                                                                <i class="bi bi-trash"></i>
                                                                Delete
                                                            </a> --}}
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
