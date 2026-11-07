<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id'); // Get user ID from route

        return [
            'full_name' => 'required|string|max:255',
            'email'     => 'required|email|unique:admins,email,' . $id,
            'role'      => 'required|exists:roles,name',
            'status'    => 'nullable',
        ];
    }
}
