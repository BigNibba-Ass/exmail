<?php

namespace App\Http\Requests\Admin\Users;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'email' => ['required', 'email', 'max:254'],
            'password' => ['required', 'confirmed'],
            'remember_token' => ['nullable'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
