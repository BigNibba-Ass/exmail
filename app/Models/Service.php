<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = ['name', 'company_id'];


    public function areas(): HasMany
    {
        return $this->hasMany(Area::class, 'service_id');
    }

    public function areaPrices(): HasMany
    {
        return $this->hasMany(AreaPrice::class, 'service_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
