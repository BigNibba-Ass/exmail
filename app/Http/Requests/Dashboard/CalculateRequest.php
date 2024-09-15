<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class CalculateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'exmail_service_id' => ['required', 'int'],
            'where_from' => ['required', 'int'],
            'where_to' => ['required', 'int'],
            'weight' => ['required', 'numeric', 'min:0.01'],
            'selected_comparable_services' => ['required', 'array'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
