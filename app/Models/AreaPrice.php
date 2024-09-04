<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AreaPrice extends Model
{
    protected $fillable = ['service_id', 'area_number', 'weight_min', 'weight_max', 'price', 'price_per_extra_kg'];
}
