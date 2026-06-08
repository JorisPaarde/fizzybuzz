<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceSubmission extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'user_id',
        'product_id',
        'wholesaler_id',
        'price',
        'unit',
        'quantity_per_unit',
        'specification',
        'effective_date',
        'source',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'effective_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
