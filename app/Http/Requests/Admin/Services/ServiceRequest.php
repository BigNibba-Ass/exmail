<?php

namespace App\Http\Requests\Admin\Services;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'company_id' => ['required', 'int'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
