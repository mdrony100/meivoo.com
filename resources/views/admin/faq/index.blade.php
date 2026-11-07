@extends('admin.layouts.app')

@section('content')
@php
    $faqs = data_get($data ?? [], 'faqs', collect());
@endphp
<div class="card content-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="card-title mb-0">FAQ Management</h2>
        <button type="button" class="btn btn-primary btn-sm btn-icon" data-bs-toggle="modal" data-bs-target="#faqCreateModal">
            <i class="bi bi-plus-lg"></i>Add FAQ
        </button>
    </div>
    <div class="card-body p-0">
        @if($faqs->count())
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                    <tr>
                        <th>Order</th>
                        <th>Question</th>
                        <th>Status</th>
                        <th>Updated</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($faqs as $faq)
                        <tr>
                            <td>{{ $faq->order_id ?? $loop->iteration }}</td>
                            <td>
                                <div class="fw-semibold">{{ $faq->title }}</div>
                                <div class="text-secondary small">{{ str($faq->body)->stripTags()->limit(90) }}</div>
                            </td>
                            <td><span class="badge {{ ($faq->status ?? 1) ? 'text-bg-success' : 'text-bg-secondary' }}">{{ ($faq->status ?? 1) ? 'Active' : 'Inactive' }}</span></td>
                            <td>{{ optional($faq->updated_at)->format('M d, Y') ?? '-' }}</td>
                            <td>
                                <div class="action-buttons">
                                    <button type="button" class="btn btn-sm btn-outline-secondary btn-icon" data-load-modal="{{ route('admin.faq.view', $faq->id) }}" data-modal-title="FAQ Details"><i class="bi bi-eye"></i>View</button>
                                    <button type="button" class="btn btn-sm btn-outline-primary btn-icon" data-load-modal="{{ route('admin.faq.edit', $faq->id) }}" data-modal-title="Edit FAQ"><i class="bi bi-pencil"></i>Edit</button>
                                    <a href="#" class="btn btn-sm btn-outline-danger btn-icon" data-confirm-url="{{ route('admin.faq.delete', $faq->id) }}" data-confirm-title="Delete FAQ" data-confirm-message="Delete this FAQ?"><i class="bi bi-trash"></i>Delete</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            @include('admin.components.empty-state', ['title' => 'No FAQs found', 'message' => 'Add questions and answers for the public FAQ section.', 'icon' => 'bi-question-circle'])
        @endif
    </div>
</div>

<div class="modal fade" id="faqCreateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.faq.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add FAQ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @include('admin.faq._form')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-icon"><i class="bi bi-check2"></i>Save FAQ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="remoteContentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="remoteContentTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="remoteContentBody"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('click', async function (event) {
        const trigger = event.target.closest('[data-load-modal]');
        if (!trigger) {
            return;
        }

        document.getElementById('remoteContentTitle').textContent = trigger.dataset.modalTitle || '';
        document.getElementById('remoteContentBody').innerHTML = '<div class="text-center py-4 text-secondary">Loading...</div>';
        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('remoteContentModal'));
        modal.show();

        const response = await fetch(trigger.dataset.loadModal, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        document.getElementById('remoteContentBody').innerHTML = await response.text();
    });
</script>
@endpush
