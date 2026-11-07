<div class="empty-state">
    <i class="bi {{ $icon ?? 'bi-inbox' }}"></i>
    <div>
        <h2 class="h5 mb-1">{{ $title ?? 'No records found' }}</h2>
        <p class="mb-0">{{ $message ?? 'Create a new record to get started.' }}</p>
    </div>
</div>
