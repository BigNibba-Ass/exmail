<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    const EXMAIL_COMPANY_ID = 1;

    protected $fillable = ['id', 'name'];

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }
}
