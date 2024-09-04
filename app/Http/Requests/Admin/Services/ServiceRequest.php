<?php

namespace App\Http\Requests\Admin\Services;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'type' => ['required', 'string'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
