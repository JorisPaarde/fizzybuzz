<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PriceImport extends Model
{
    public const SOURCE_MANUAL = 'manual';

    public const SOURCE_PHOTO = 'photo';

    public const SOURCE_PDF = 'pdf';

    public const SOURCE_EMAIL = 'email';

    public const STATUS_EXTRACTING = 'extracting';

    public const STATUS_REVIEW = 'review';

    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'user_id',
        'source',
        'original_filename',
        'file_path',
        'wholesaler_id',
        'effective_date',
        'extracted_items',
        'status',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'effective_date' => 'date',
            'extracted_items' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function wholesaler(): BelongsTo
    {
        return $this->belongsTo(Wholesaler::class);
    }

    public function priceSubmissions(): HasMany
    {
        return $this->hasMany(PriceSubmission::class);
    }
}
