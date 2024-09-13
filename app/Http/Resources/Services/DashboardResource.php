<?php

namespace App\Http\Resources\Services;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Service */
class DashboardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'text' => $this->name,
            'value' => $this->type,
        ];
    }
}
