<?php

namespace App\Http\Requests\Admin\Informations;

use Illuminate\Foundation\Http\FormRequest;

class DataRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'service_id' => ['required', 'integer'],
            'file' => ['required']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
