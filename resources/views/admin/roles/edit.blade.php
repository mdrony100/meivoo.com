@extends('admin.layouts.app')

@section('content')
<div class="card content-card">
    <div class="card-header">
        <h2 class="card-title mb-0">Edit Role</h2>
    </div>
    <form method="POST" action="{{ route('admin.roles.update', $role->id) }}">
        @csrf
        @include('admin.roles._form', ['buttonText' => 'Update Role'])
    </form>
</div>
@endsection
