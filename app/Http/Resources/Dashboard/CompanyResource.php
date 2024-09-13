<?php

namespace App\Http\Resources\Dashboard;

use App\Http\Resources\CustomSelect\CustomSelectProperties;
use App\Http\Resources\CustomSelect\CustomSelectResourceCollection;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Company */
class CompanyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'discount' => 0,
            'services' => CustomSelectResourceCollection::collection(
                $this->services,
                new CustomSelectProperties('name', 'id')
            ),
        ];
    }
}
