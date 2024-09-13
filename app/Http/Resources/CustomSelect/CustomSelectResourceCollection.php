<?php

namespace App\Http\Resources\CustomSelect;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CustomSelectResourceCollection
{
    public static function collection($collection, CustomSelectProperties $properties): AnonymousResourceCollection
    {
        $instance = CustomSelectResource::collection($collection);
        $instance->map(function ($resource) use ($properties) {
            $resource->properties = $properties;
        });
        return $instance;
    }
}
