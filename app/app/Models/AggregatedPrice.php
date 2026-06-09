<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Anonieme marktstatistieken — geen koppeling naar individuele leden.
 */
class AggregatedPrice extends Model
{
    protected $fillable = [
        'product_id',
        'wholesaler_id',
        'purchase_size',
        'avg_price',
        'median_price',
        'min_price',
        'max_price',
        'datapoint_count',
        'period_start',
        'period_end',
    ];

    protected function casts(): array
    {
        return [
            'avg_price' => 'decimal:2',
            'median_price' => 'decimal:2',
            'min_price' => 'decimal:2',
            'max_price' => 'decimal:2',
            'period_start' => 'date',
            'period_end' => 'date',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function wholesaler(): BelongsTo
    {
        return $this->belongsTo(Wholesaler::class);
    }
}
