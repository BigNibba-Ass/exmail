<?php

namespace App\Models;

use Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Offer extends Model
{
    protected $fillable = ['user_id', 'data'];

    protected $casts = [
      'data' => 'object'
    ];

    protected $appends = ['created_at_in_format'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getCreatedAtInFormatAttribute(): string
    {
        return $this->created_at->format('Y-m-d H:i:s');
    }
}
