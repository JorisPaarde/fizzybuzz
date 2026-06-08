<?php

namespace App\Services;

use App\Models\AggregatedPrice;
use App\Models\PriceSubmission;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AnonymizationService
{
    public const MIN_DATAPOINTS = 3;

    /**
     * Herbereken geaggregeerde prijzen voor een product/groothandel-combinatie.
     * Alleen goedgekeurde inzendingen met minimaal MIN_DATAPOINTS unieke leden.
     */
    public function aggregate(int $productId, int $wholesalerId, ?Carbon $periodStart = null, ?Carbon $periodEnd = null): ?AggregatedPrice
    {
        $periodStart ??= now()->subDays(90)->startOfDay();
        $periodEnd ??= now()->endOfDay();

        $prices = PriceSubmission::query()
            ->where('product_id', $productId)
            ->where('wholesaler_id', $wholesalerId)
            ->where('status', PriceSubmission::STATUS_APPROVED)
            ->whereBetween('effective_date', [$periodStart, $periodEnd])
            ->pluck('price')
            ->map(fn ($price) => (float) $price)
            ->sort()
            ->values();

        $datapointCount = PriceSubmission::query()
            ->where('product_id', $productId)
            ->where('wholesaler_id', $wholesalerId)
            ->where('status', PriceSubmission::STATUS_APPROVED)
            ->whereBetween('effective_date', [$periodStart, $periodEnd])
            ->distinct('user_id')
            ->count('user_id');

        if ($datapointCount < self::MIN_DATAPOINTS || $prices->isEmpty()) {
            return null;
        }

        return AggregatedPrice::updateOrCreate(
            [
                'product_id' => $productId,
                'wholesaler_id' => $wholesalerId,
                'period_start' => $periodStart->toDateString(),
                'period_end' => $periodEnd->toDateString(),
            ],
            [
                'avg_price' => round($prices->avg(), 2),
                'median_price' => $this->median($prices),
                'min_price' => $prices->min(),
                'max_price' => $prices->max(),
                'datapoint_count' => $datapointCount,
            ]
        );
    }

    /**
     * Publieke marktdata — nooit gekoppeld aan individuele leden.
     */
    public function getPublicComparisons(int $productId, ?int $wholesalerId = null): Collection
    {
        return AggregatedPrice::query()
            ->with(['product', 'wholesaler'])
            ->where('product_id', $productId)
            ->when($wholesalerId, fn ($query) => $query->where('wholesaler_id', $wholesalerId))
            ->where('datapoint_count', '>=', self::MIN_DATAPOINTS)
            ->orderByDesc('period_end')
            ->get();
    }

    private function median(Collection $values): float
    {
        $count = $values->count();
        $middle = intdiv($count, 2);

        if ($count % 2) {
            return round($values[$middle], 2);
        }

        return round(($values[$middle - 1] + $values[$middle]) / 2, 2);
    }
}
