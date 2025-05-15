<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $fillable = ['area_number', 'service_id', 'where_from', 'where_to', 'weight_max', 'price_nds', 'price_nds_free', 'terms'];

    public function departurePointFrom(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DeparturePoint::class, 'where_from');
    }

    public function departurePointTo(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DeparturePoint::class, 'where_to');
    }
}
