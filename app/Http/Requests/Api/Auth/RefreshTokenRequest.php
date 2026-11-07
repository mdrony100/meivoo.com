<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\ApiFormRequest;

class RefreshTokenRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'refresh_token' => ['nullable', 'string'],
        ];
    }
}
