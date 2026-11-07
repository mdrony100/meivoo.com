@extends('admin.layouts.app')

@section('newsletter', 'active')

@section('content')
@php
    $subscribers = data_get($data ?? [], 'rows', collect());
@endphp

<div class="card content-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="card-title mb-0">Newsletter Subscribers</h2>

        <span class="badge text-bg-primary">
            Total: {{ $subscribers->count() }}
        </span>
    </div>

    <div class="card-body p-0">
        @if($subscribers->count())
            <div class="table-responsive p-3">
                <table class="table table-hover mb-0" id="dataTables">
                    <thead>
                        <tr>
                            <th>SN</th>
                            <th>Email Address</th>
                            <th>Subscribed On</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($subscribers as $subscriber)
                            <tr>
                                <td>
                                    {{ $loop->iteration }}
                                </td>
                                <td>
                                    <div class="fw-semibold">
                                        <a href="mailto:{{ $subscriber->email }}">{{ $subscriber->email }}</a>
                                    </div>
                                </td>

                                <td>
                                    {{ optional($subscriber->created_at)->format('M d, Y h:i A') ?? '-' }}
                                </td>

                                <td>
                                    <div class="action-buttons justify-content-end">
                                        {{-- <button
                                            type="button"
                                            class="btn btn-sm btn-outline-secondary btn-icon"
                                            data-bs-toggle="modal"
                                            data-bs-target="#subscriberModal{{ $subscriber->id }}">
                                            <i class="bi bi-eye"></i>
                                            View
                                        </button> --}}

                                        <a href="#"
                                           class="btn btn-sm btn-outline-danger btn-icon"
                                           data-confirm-url="{{ route('admin.subscribers.delete', $subscriber->id) }}"
                                           data-confirm-title="Delete Subscriber"
                                           data-confirm-message="Are you sure you want to delete this subscriber?">
                                            <i class="bi bi-trash"></i>
                                            Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            @include('admin.components.empty-state', [
                'title'   => 'No subscribers found',
                'message' => 'Newsletter subscribers will appear here.',
                'icon'    => 'bi-envelope-paper'
            ])
        @endif
    </div>
</div>

@foreach($subscribers as $subscriber)
    <div class="modal fade"
         id="subscriberModal{{ $subscriber->id }}"
         tabindex="-1"
         aria-hidden="true">

        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        Subscriber Details
                    </h5>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <dl class="row mb-0">

                        <dt class="col-sm-4">
                            Email
                        </dt>
                        <dd class="col-sm-8">
                            {{ $subscriber->email }}
                        </dd>

                        <dt class="col-sm-4">
                            Subscribed At
                        </dt>
                        <dd class="col-sm-8">
                            {{ optional($subscriber->created_at)->format('M d, Y h:i A') }}
                        </dd>

                        {{-- <dt class="col-sm-4">
                            Updated At
                        </dt>
                        <dd class="col-sm-8">
                            {{ optional($subscriber->updated_at)->format('M d, Y h:i A') }}
                        </dd> --}}

                    </dl>
                </div>

            </div>
        </div>
    </div>
@endforeach
@endsection
