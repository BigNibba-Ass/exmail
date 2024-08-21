<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Areas extends Model
{
    protected $fillable = ['name', 'where_from', 'where_to', 'service_id', 'weight_max', 'price_nds', 'price_nds_free', 'terms'];
}
