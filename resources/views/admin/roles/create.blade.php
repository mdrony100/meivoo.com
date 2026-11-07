@extends('admin.layouts.app')

@section('content')
<div class="card content-card">
    <div class="card-header">
        <h2 class="card-title mb-0">Create Role</h2>
    </div>
    <form method="POST" action="{{ route('admin.roles.store') }}">
        @csrf
        @include('admin.roles._form', ['buttonText' => 'Create Role'])
    </form>
</div>
@endsection
