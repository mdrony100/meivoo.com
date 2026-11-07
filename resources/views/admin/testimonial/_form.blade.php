@php
    $testimonial = $testimonial ?? null;
    $selectedStatus = (string) old('status', $testimonial->status ?? 1);
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label required" for="name">Name</label>
        <input type="text"
               name="name"
               id="name"
               value="{{ old('name', $testimonial->name ?? '') }}"
               class="form-control @error('name') is-invalid @enderror"
               placeholder="Enter client name"
               required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label" for="status">Status</label>
        <select name="status"
                id="status"
                class="form-select @error('status') is-invalid @enderror"
                required>
            <option value="1" {{ $selectedStatus === '1' ? 'selected' : '' }}>Active</option>
            <option value="0" {{ $selectedStatus === '0' ? 'selected' : '' }}>Inactive</option>
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label class="form-label required" for="message">Message</label>
        <textarea name="message"
                  id="message"
                  rows="5"
                  class="form-control @error('message') is-invalid @enderror"
                  placeholder="Write testimonial message"
                  required>{{ old('message', $testimonial->message ?? '') }}</textarea>
        @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-12">
        <label class="form-label {{ $testimonial ? '' : 'required' }}" for="image">Image</label>
        <input type="file"
               name="image"
               id="image"
               class="form-control @error('image') is-invalid @enderror"
               accept="image/*"
               {{ $testimonial ? '' : 'required' }}>
        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    @if($testimonial && $testimonial->image)
        <div class="col-md-6">
            <label class="form-label d-block">Current Image</label>
            <img src="{{ asset($testimonial->image) }}"
                 alt="{{ $testimonial->name }}"
                 class="rounded border"
                 width="100"
                 height="100"
                 style="object-fit: cover;">
        </div>
    @endif
</div>
