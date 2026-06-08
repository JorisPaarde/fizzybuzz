<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

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

    public static function findOrCreateFromName(string $name): self
    {
        $normalized = trim($name);

        return static::query()->firstOrCreate(
            ['slug' => Str::slug($normalized)],
            [
                'name' => $normalized,
                'standard_unit' => 'stuk',
            ]
        );
    }
}
