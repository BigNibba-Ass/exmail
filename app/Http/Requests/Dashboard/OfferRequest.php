<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class OfferRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'data' => ['required'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
