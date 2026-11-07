<style>
    /* Modern Clean Form Layout */
    .legacy-form-wrapper {
        max-width: 720px;
        margin: 30px auto;
        padding: 0 15px;
    }

    .legacy-form-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
        overflow: hidden;
    }

    .legacy-form-header {
        padding: 20px 28px;
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .legacy-form-header .title-area h3 {
        margin: 0;
        font-size: 1.15rem;
        font-weight: 700;
        color: #111827;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        color: #4b5563;
        background: #ffffff;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-back:hover {
        color: #6366f1;
        background: #f3f4f6;
        border-color: #6366f1;
    }

    .legacy-form-body {
        padding: 20px;
    }

    .form-group-custom {
        margin-bottom: 20px;
    }

    .form-group-custom label {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        color: #374151;
    }

    .form-control-custom {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        font-size: 0.925rem;
        color: #1f2937;
        background-color: #f9fafb;
        outline: none;
        transition: all 0.2s ease-in-out;
        box-sizing: border-box;
    }

    .form-control-custom:focus {
        background-color: #ffffff;
        border-color: #6366f1;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
    }

    textarea.form-control-custom {
        resize: vertical;
        min-height: 100px;
    }

    .btn-submit-custom {
        width: 100%;
        padding: 12px 20px;
        color: #ffffff;
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        border: none;
        border-radius: 10px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-submit-custom:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(99, 102, 241, 0.35);
    }

    .invalid-feedback {
        color: #ef4444;
        font-size: 0.8rem;
        margin-top: 5px;
        font-weight: 500;
        display: block;
    }

    .is-invalid {
        border-color: #ef4444 !important;
        background-color: #fef2f2 !important;
    }
</style>

<form action="{{ route('user.legacy-names.update', $legacyName->id) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="legacy-form-body">

        {{-- Name --}}
        <div class="form-group-custom">
            <label>
                <i class="fa-solid fa-signature text-muted"></i> Name <span class="text-danger">*</span>
            </label>
            <input type="text" name="name" class="form-control-custom @error('name') is-invalid @enderror"
                value="{{ old('name', $legacyName->name) }}" required maxlength="255" placeholder="Enter full name">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Name Meaning --}}
        <div class="form-group-custom">
            <label>
                <i class="fa-solid fa-book-open text-muted"></i> Name Meaning
            </label>
            <textarea name="meaning" class="form-control-custom @error('meaning') is-invalid @enderror" maxlength="1000"
                placeholder="What does this name mean?">{{ old('meaning', $legacyName->meaning) }}</textarea>
            @error('meaning')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Story Behind the Name --}}
        <div class="form-group-custom">
            <label>
                <i class="fa-solid fa-feather-pointed text-muted"></i> Story Behind the Name
            </label>
            <textarea name="story_behind_the_name" class="form-control-custom @error('story_behind_the_name') is-invalid @enderror"
                maxlength="3000" placeholder="Share the history or special story behind this name...">{{ old('story_behind_the_name', $legacyName->story_behind_the_name) }}</textarea>
            @error('story_behind_the_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Grid for 2 columns --}}
        <div class="row g-3">
            <div class="col-md-6 col-12">
                {{-- Who Gave This Name --}}
                <div class="form-group-custom mb-md-0">
                    <label>
                        <i class="fa-solid fa-user-tag text-muted"></i> Who Gave This Name
                    </label>
                    <input type="text" name="who_gave_this_name"
                        class="form-control-custom @error('who_gave_this_name') is-invalid @enderror"
                        value="{{ old('who_gave_this_name', $legacyName->who_gave_this_name) }}" maxlength="255" placeholder="e.g., Grandmother, Father">
                    @error('who_gave_this_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6 col-12">
                {{-- Family Generation --}}
                <div class="form-group-custom mb-md-0">
                    <label>
                        <i class="fa-solid fa-sitemap text-muted"></i> Family Generation
                    </label>
                    <select name="family_generation"
                        class="form-control-custom @error('family_generation') is-invalid @enderror">
                        <option value="">Select generation</option>
                        <option value="Grandparent" {{ old('family_generation', $legacyName->family_generation) === 'Grandparent' ? 'selected' : '' }}>
                            Grandparent</option>
                        <option value="Parent" {{ old('family_generation', $legacyName->family_generation) === 'Parent' ? 'selected' : '' }}>Parent
                        </option>
                        <option value="Elder" {{ old('family_generation', $legacyName->family_generation) === 'Elder' ? 'selected' : '' }}>Elder
                        </option>
                    </select>
                    @error('family_generation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="pt-4 d-flex justify-content-end mt-2">
            <button type="submit" class="btn-post" style="padding: 15px 20px">
                <i class="fa-solid fa-pen-to-square"></i>
                <span>Update Legacy Name</span>
            </button>
        </div>

    </div>
</form>