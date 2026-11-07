@extends('admin.layouts.app')

@section('content')
<div class="card content-card">
    <div class="card-header"><h2 class="card-title mb-0">Create FAQ</h2></div>
    <form method="POST" action="{{ route('admin.faq.store') }}">
        @csrf
        <div class="card-body">@include('admin.faq._form')</div>
        <div class="card-footer text-end">
            <button type="submit" class="btn btn-primary btn-icon"><i class="bi bi-check2"></i>Save FAQ</button>
        </div>
    </form>
</div>
@endsection
