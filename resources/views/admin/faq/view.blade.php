<div>
    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
        <h3 class="h5 mb-0">{{ $faq->title }}</h3>
        <span class="badge {{ ($faq->status ?? 1) ? 'text-bg-success' : 'text-bg-secondary' }}">{{ ($faq->status ?? 1) ? 'Active' : 'Inactive' }}</span>
    </div>
    <div class="text-secondary mb-3">Order: {{ $faq->order_id ?? '-' }}</div>
    <div class="border rounded p-3 bg-body-tertiary">{!! nl2br(e($faq->body)) !!}</div>
</div>
