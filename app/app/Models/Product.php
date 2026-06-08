<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'category',
        'standard_unit',
    ];

    public function priceSubmissions(): HasMany
    {
        return $this->hasMany(PriceSubmission::class);
    }

    public function aggregatedPrices(): HasMany
    {
        return $this->hasMany(AggregatedPrice::class);
    }
}
