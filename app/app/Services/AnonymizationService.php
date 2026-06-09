<?php

namespace App\Services;

use App\Enums\PurchaseSize;
use App\Models\AggregatedPrice;
use App\Models\PriceSubmission;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AnonymizationService
{
    public const MIN_DATAPOINTS = 3;

    /**
     * Herbereken geaggregeerde prijzen per inkoopgrootte-segment.
     */
    public function aggregate(int $productId, int $wholesalerId, ?Carbon $periodStart = null, ?Carbon $periodEnd = null): void
    {
        $periodStart ??= now()->subDays(90)->startOfDay();
        $periodEnd ??= now()->endOfDay();

        $sizes = PriceSubmission::query()
            ->where('product_id', $productId)
            ->where('wholesaler_id', $wholesalerId)
            ->where('status', PriceSubmission::STATUS_APPROVED)
            ->whereBetween('effective_date', [$periodStart, $periodEnd])
            ->join('users', 'users.id', '=', 'price_submissions.user_id')
            ->whereNotNull('users.purchase_size')
            ->distinct()
            ->pluck('users.purchase_size');

        foreach ($sizes as $purchaseSize) {
            $this->aggregateSegment($productId, $wholesalerId, (string) $purchaseSize, $periodStart, $periodEnd);
        }
    }

    /**
     * Herbereken geaggregeerde prijzen voor één product/groothandel/inkoopgrootte-combinatie.
     */
    public function aggregateSegment(
        int $productId,
        int $wholesalerId,
        string $purchaseSize,
        ?Carbon $periodStart = null,
        ?Carbon $periodEnd = null,
    ): ?AggregatedPrice {
        $periodStart ??= now()->subDays(90)->startOfDay();
        $periodEnd ??= now()->endOfDay();

        $submissionQuery = PriceSubmission::query()
            ->where('product_id', $productId)
            ->where('wholesaler_id', $wholesalerId)
            ->where('status', PriceSubmission::STATUS_APPROVED)
            ->whereBetween('effective_date', [$periodStart, $periodEnd])
            ->whereHas('user', fn ($query) => $query->where('purchase_size', $purchaseSize));

        $prices = (clone $submissionQuery)
            ->pluck('price')
            ->map(fn ($price) => (float) $price)
            ->sort()
            ->values();

        $datapointCount = (clone $submissionQuery)
            ->distinct('user_id')
            ->count('user_id');

        if ($datapointCount < self::MIN_DATAPOINTS || $prices->isEmpty()) {
            AggregatedPrice::query()
                ->where('product_id', $productId)
                ->where('wholesaler_id', $wholesalerId)
                ->where('purchase_size', $purchaseSize)
                ->where('period_start', $periodStart->toDateString())
                ->where('period_end', $periodEnd->toDateString())
                ->delete();

            return null;
        }

        return AggregatedPrice::updateOrCreate(
            [
                'product_id' => $productId,
                'wholesaler_id' => $wholesalerId,
                'purchase_size' => $purchaseSize,
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
     * Live marktstatistiek voor vergelijking — exclusief het eigen bedrijf.
     *
     * @return array{
     *     avg_price: float,
     *     median_price: float,
     *     min_price: float,
     *     max_price: float,
     *     datapoint_count: int
     * }|null
     */
    public function getComparisonStatsExcludingUser(
        int $productId,
        int $wholesalerId,
        string $purchaseSize,
        int $excludeUserId,
        ?Carbon $periodStart = null,
        ?Carbon $periodEnd = null,
    ): ?array {
        return $this->getSegmentStats(
            $productId,
            $wholesalerId,
            $purchaseSize,
            $periodStart,
            $periodEnd,
            $excludeUserId,
        );
    }

    /**
     * Geaggregeerde marktstatistiek voor een segment (optioneel zonder één lid).
     *
     * @return array{
     *     avg_price: float,
     *     median_price: float,
     *     min_price: float,
     *     max_price: float,
     *     datapoint_count: int
     * }|null
     */
    public function getSegmentStats(
        int $productId,
        int $wholesalerId,
        string $purchaseSize,
        ?Carbon $periodStart = null,
        ?Carbon $periodEnd = null,
        ?int $excludeUserId = null,
    ): ?array {
        $periodStart ??= now()->subDays(90)->startOfDay();
        $periodEnd ??= now()->endOfDay();

        $submissionQuery = PriceSubmission::query()
            ->where('product_id', $productId)
            ->where('wholesaler_id', $wholesalerId)
            ->where('status', PriceSubmission::STATUS_APPROVED)
            ->whereBetween('effective_date', [$periodStart, $periodEnd])
            ->whereHas('user', fn ($query) => $query->where('purchase_size', $purchaseSize))
            ->when($excludeUserId, fn ($query) => $query->where('user_id', '!=', $excludeUserId));

        $prices = (clone $submissionQuery)
            ->pluck('price')
            ->map(fn ($price) => (float) $price)
            ->sort()
            ->values();

        $datapointCount = (clone $submissionQuery)
            ->distinct('user_id')
            ->count('user_id');

        if ($datapointCount < self::MIN_DATAPOINTS || $prices->isEmpty()) {
            return null;
        }

        return [
            'avg_price' => round($prices->avg(), 2),
            'median_price' => $this->median($prices),
            'min_price' => $prices->min(),
            'max_price' => $prices->max(),
            'datapoint_count' => $datapointCount,
        ];
    }

    /**
     * Publieke marktdata — gefilterd op inkoopgrootte, nooit gekoppeld aan individuele leden.
     */
    public function getPublicComparisons(
        int $productId,
        ?string $purchaseSize = null,
        ?int $wholesalerId = null,
    ): Collection {
        return AggregatedPrice::query()
            ->with(['product', 'wholesaler'])
            ->where('product_id', $productId)
            ->when($purchaseSize, fn ($query) => $query->where('purchase_size', $purchaseSize))
            ->when($wholesalerId, fn ($query) => $query->where('wholesaler_id', $wholesalerId))
            ->where('datapoint_count', '>=', self::MIN_DATAPOINTS)
            ->orderByDesc('period_end')
            ->get();
    }

    /**
     * @return array<string, string>
     */
    public static function purchaseSizeLabels(): array
    {
        return PurchaseSize::options();
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
