@php
    $faq = $faq ?? null;
@endphp
<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label required" for="question">Question</label>
        <input type="text" name="question" id="question" value="{{ old('question', $faq->title ?? '') }}" class="form-control @error('question') is-invalid @enderror" required>
        @error('question')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-2">
        <label class="form-label" for="order_number">Order</label>
        <input type="number" name="order_number" id="order_number" value="{{ old('order_number', $faq->order_id ?? '') }}" class="form-control">
    </div>
    <div class="col-md-2">
        <label class="form-label" for="status">Status</label>
        <select name="status" id="status" class="form-select">
            <option value="1" {{ (string) old('status', $faq->status ?? 1) === '1' ? 'selected' : '' }}>Active</option>
            <option value="0" {{ (string) old('status', $faq->status ?? 1) === '0' ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>
    <div class="col-12">
        <label class="form-label required" for="answer">Answer</label>
        <textarea name="answer" id="answer" rows="6" class="form-control @error('answer') is-invalid @enderror" required>{{ old('answer', $faq->body ?? '') }}</textarea>
        @error('answer')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
