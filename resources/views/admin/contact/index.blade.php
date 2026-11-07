@extends('admin.layouts.app')

@section('contact', 'active')

@section('content')
@php
    $contacts = data_get($data ?? [], 'rows', collect());
@endphp
<div class="card content-card">
    <div class="card-header">
        <h2 class="card-title mb-0">Contacts</h2>
    </div>
    <div class="card-body p-0">
        @if($contacts->count())
            <div class="table-responsive p-3">
                <table class="table table-hover mb-0" id="dataTables">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Subject</th>
                        {{-- <th>Phone</th> --}}
                        {{-- <th>Reply</th> --}}
                        <th>Received</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($contacts as $contact)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $contact->name ?? '-' }}</div>
                                <div class="text-secondary small">
                                    <a href="mailto:{{ $contact->email }}">{{ $contact->email ?? '-' }}</a>
                                </div>
                            </td>
                            <td>
                                <div>{{ $contact->subject ?? '-' }}</div>
                                <div class="text-secondary small">{{ str($contact->message ?? '')->limit(80) }}</div>
                            </td>
                            {{-- <td>{{ $contact->phone ?? '-' }}</td> --}}
                            {{-- <td><span class="badge {{ ($contact->reply_status ?? 0) ? 'text-bg-success' : 'text-bg-warning' }}">{{ ($contact->reply_status ?? 0) ? 'Replied' : 'Pending' }}</span></td> --}}
                            <td>{{ optional($contact->created_at)->format('M d, Y') ?? '-' }}</td>
                            <td>
                                <div class="action-buttons">
                                    <button type="button" class="btn btn-sm btn-outline-secondary btn-icon" data-bs-toggle="modal" data-bs-target="#contactModal{{ $contact->id }}"><i class="bi bi-eye"></i>View</button>
                                    <a href="#" class="btn btn-sm btn-outline-danger btn-icon" data-confirm-url="{{ route('admin.contact.delete', $contact->id) }}" data-confirm-title="Delete Contact" data-confirm-message="Delete this contact message?"><i class="bi bi-trash"></i>Delete</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            @include('admin.components.empty-state', ['title' => 'No contacts found', 'message' => 'Contact form submissions will appear here.', 'icon' => 'bi-envelope'])
        @endif
    </div>
</div>

@foreach($contacts as $contact)
    <div class="modal fade" id="contactModal{{ $contact->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Contact Message</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <dl class="row">
                        <dt class="col-sm-3">Name</dt>
                        <dd class="col-sm-9">{{ $contact->name ?? '-' }}</dd>
                        <dt class="col-sm-3">Email</dt>
                        <dd class="col-sm-9">{{ $contact->email ?? '-' }}</dd>
                        {{-- <dt class="col-sm-3">Phone</dt>
                        <dd class="col-sm-9">{{ $contact->phone ?? '-' }}</dd> --}}
                        {{-- <dt class="col-sm-3">Company</dt>
                        <dd class="col-sm-9">{{ $contact->company_name ?? '-' }}</dd> --}}
                        <dt class="col-sm-3">Subject</dt>
                        <dd class="col-sm-9">{{ $contact->subject ?? '-' }}</dd>
                    </dl>
                    <div class="border rounded p-3 bg-body-tertiary">{!! nl2br(e($contact->message ?? '')) !!}</div>
                </div>
            </div>
        </div>
    </div>
@endforeach
@endsection
