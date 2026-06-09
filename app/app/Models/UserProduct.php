<?php

namespace App\Models;

use App\Enums\AddedVia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProduct extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'added_via',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'added_via' => AddedVia::class,
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
}
