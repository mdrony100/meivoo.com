<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\ApiFormRequest;

class LogoutRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [];
    }
}
