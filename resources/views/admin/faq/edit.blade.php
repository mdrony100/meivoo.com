<form method="POST" action="{{ route('admin.faq.update', $faq->id) }}">
    @csrf
    @include('admin.faq._form', ['faq' => $faq])
    <div class="text-end mt-3">
        <button type="submit" class="btn btn-primary btn-icon"><i class="bi bi-check2"></i>Update FAQ</button>
    </div>
</form>
