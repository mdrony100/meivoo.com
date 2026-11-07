@extends('layouts.frontend')

@section('title', $page->meta_title ?? $page->title)

@section('meta')
    <meta property="og:title" content="{{ $page->meta_title }}">
    <meta property="og:description" content="{{ $page->meta_description }}">
    <meta property="og:image" content="{{ asset($settings->seo_image) }}">
    <meta name="description" content="{{ $page->meta_description }}">
    <meta name="keywords" content="{{ $page->meta_keywords }}">
@endsection

@section('content')
    <section class="py-5">
        <div class="container">
            <h1 class="mb-4">{{ $page->title }}</h1>
            {!! $page->body !!}
        </div>
    </section>
@endsection

@push('scripts')

@endpush