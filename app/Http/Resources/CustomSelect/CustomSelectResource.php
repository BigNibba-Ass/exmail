<?php

namespace App\Http\Resources\CustomSelect;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomSelectResource extends JsonResource
{
    public CustomSelectProperties $properties;

    public function toArray(Request $request): array
    {
        $textField = $this->properties->getTextFieldName();
        $valueField = $this->properties->getValueFieldName();
        return [
            'text' => $this->$textField,
            'value' => $this->$valueField,
        ];
    }
}
