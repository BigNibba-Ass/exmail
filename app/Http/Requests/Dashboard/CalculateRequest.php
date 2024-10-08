<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class CalculateRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'exmail_service_id' => ['required', 'int'],
            'calculation_items' => ['required', 'array'],
            'selected_comparable_services' => ['required', 'array'],
            'nds_included' => ['nullable', 'bool'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
