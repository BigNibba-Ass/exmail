<?php

namespace App\Http\Requests\Admin\Users;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends UserRequest
{
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'password' => ['nullable', 'confirmed'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
