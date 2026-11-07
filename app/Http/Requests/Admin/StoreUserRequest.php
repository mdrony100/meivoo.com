<?php

namespace app\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'full_name'     => 'required|max:255',
            'email'         => 'required|email:rfc,dns|unique:admins,email',
            'image'         => 'nullable|image',
            'role'          => 'required|exists:roles,name',
            'password'      => 'required|max:16',
            'status'        => 'nullable',
            // 'username'   => 'required|unique:users,username',
        ];
    }
}
