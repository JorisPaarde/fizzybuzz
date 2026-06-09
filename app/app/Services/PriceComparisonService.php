<?php

namespace App\Services;

use App\Data\ComparisonFilters;
use App\Models\PriceSubmission;
use App\Models\Product;
use App\Models\User;
use App\Models\Wholesaler;
use Illuminate\Support\Collection;

class PriceComparisonService
{
    public function __construct(
        private readonly AnonymizationService $anonymizationService,
    ) {}

    /**
     * Groothandels waar het lid prijsdata voor heeft — voor filterdropdown.
     */
    public function getWholesalersForFilter(User $user): Collection
    {
        $wholesalerIds = PriceSubmission::query()
            ->where('user_id', $user->id)
            ->where('status', PriceSubmission::STATUS_APPROVED)
            ->distinct()
            ->pluck('wholesaler_id');

        return Wholesaler::query()
            ->whereIn('id', $wholesalerIds)
            ->orderBy('name')
            ->get();
    }

    /**
     * Zoek producten op naam — eigen prijzen en/of producten met marktdata.
     */
    public function searchProducts(
        User $user,
        ?string $query = null,
        ?ComparisonFilters $filters = null,
        int $limit = 20,
    ): Collection {
        $filters ??= new ComparisonFilters;
        $periodStart = $filters->periodStart();
        $periodEnd = $filters->periodEnd();

        $builder = Product::query()
            ->where(function ($q) use ($user, $filters, $periodStart, $periodEnd) {
                $q->whereHas('priceSubmissions', fn ($s) => $s
                    ->where('user_id', $user->id)
                    ->where('status', PriceSubmission::STATUS_APPROVED)
                    ->whereBetween('effective_date', [$periodStart, $periodEnd])
                    ->when(
                        $filters->wholesalerId,
                        fn ($query) => $query->where('wholesaler_id', $filters->wholesalerId)
                    ))
                    ->orWhereHas('aggregatedPrices', fn ($a) => $a
                        ->where('datapoint_count', '>=', AnonymizationService::MIN_DATAPOINTS)
                        ->when(
                            $user->purchase_size,
                            fn ($query) => $query->where('purchase_size', $user->purchase_size)
                        )
                        ->when(
                            $filters->wholesalerId,
                            fn ($query) => $query->where('wholesaler_id', $filters->wholesalerId)
                        ));
            });

        if (filled($query)) {
            $builder->where(function ($q) use ($query) {
                $q->where('name', 'like', '%'.$query.'%')
                    ->orWhere('slug', 'like', '%'.str($query)->slug().'%');
            });
        }

        return $builder
            ->orderBy('name')
            ->limit($limit)
            ->get();
    }

    /**
     * Dashboard-inzicht: producten boven marktrange en totalen.
     *
     * @return array{
     *     total_above: int,
     *     total_within: int,
     *     total_below: int,
     *     total_no_data: int,
     *     above_market: array<int, array{
     *         product: Product,
     *         wholesaler_id: int,
     *         wholesaler_name: string,
     *         user_price: float,
     *         user_unit: string,
     *         difference_from_max_percent: float|null
     *     }>
     * }
     */
    public function getMarketInsights(User $user, ?ComparisonFilters $filters = null): array
    {
        $filters ??= new ComparisonFilters;
        $periodStart = $filters->periodStart();
        $periodEnd = $filters->periodEnd();

        $submissions = PriceSubmission::query()
            ->with(['product', 'wholesaler'])
            ->where('user_id', $user->id)
            ->where('status', PriceSubmission::STATUS_APPROVED)
            ->whereBetween('effective_date', [$periodStart, $periodEnd])
            ->when(
                $filters->wholesalerId,
                fn ($query) => $query->where('wholesaler_id', $filters->wholesalerId)
            )
            ->orderByDesc('effective_date')
            ->get()
            ->unique(fn (PriceSubmission $submission) => $submission->product_id.'-'.$submission->wholesaler_id);

        $aboveMarket = [];
        $withinMarket = 0;
        $belowMarket = 0;
        $noData = 0;

        foreach ($submissions as $submission) {
            if (! $user->purchase_size) {
                $noData++;

                continue;
            }

            $marketStats = $this->anonymizationService->getComparisonStatsExcludingUser(
                $submission->product_id,
                $submission->wholesaler_id,
                $user->purchase_size,
                $user->id,
                $periodStart,
                $periodEnd,
            );

            if ($marketStats === null) {
                $noData++;

                continue;
            }

            $evaluation = $this->evaluateRangePosition((float) $submission->price, $marketStats);

            match ($evaluation['range_position']) {
                'above' => $aboveMarket[] = [
                    'product' => $submission->product,
                    'wholesaler_id' => $submission->wholesaler_id,
                    'wholesaler_name' => $submission->wholesaler->name,
                    'user_price' => (float) $submission->price,
                    'user_unit' => $submission->unit,
                    'difference_from_max_percent' => $evaluation['difference_from_max_percent'],
                ],
                'within' => $withinMarket++,
                'below' => $belowMarket++,
                default => $noData++,
            };
        }

        usort(
            $aboveMarket,
            fn (array $a, array $b) => ($b['difference_from_max_percent'] ?? 0) <=> ($a['difference_from_max_percent'] ?? 0)
        );

        return [
            'total_above' => count($aboveMarket),
            'total_within' => $withinMarket,
            'total_below' => $belowMarket,
            'total_no_data' => $noData,
            'above_market' => array_slice($aboveMarket, 0, 10),
        ];
    }

    /**
     * Vergelijking per groothandel: eigen prijs vs. anonieme marktstatistiek.
     *
     * @return array{
     *     product: Product,
     *     purchase_size: string|null,
     *     purchase_size_label: string|null,
     *     rows: array<int, array{
     *         wholesaler_id: int,
     *         wholesaler_name: string,
     *         user_price: float|null,
     *         user_unit: string|null,
     *         market: object|null,
     *         range_position: 'below'|'within'|'above'|null,
     *         range_percent: float|null,
     *         difference_from_min_percent: float|null,
     *         difference_from_max_percent: float|null
     *     }>
     * }
     */
    public function compareProductForUser(
        User $user,
        Product $product,
        ?ComparisonFilters $filters = null,
    ): array {
        $filters ??= new ComparisonFilters;
        $periodStart = $filters->periodStart();
        $periodEnd = $filters->periodEnd();

        $userSubmissions = PriceSubmission::query()
            ->with('wholesaler')
            ->where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->where('status', PriceSubmission::STATUS_APPROVED)
            ->whereBetween('effective_date', [$periodStart, $periodEnd])
            ->when(
                $filters->wholesalerId,
                fn ($query) => $query->where('wholesaler_id', $filters->wholesalerId)
            )
            ->orderByDesc('effective_date')
            ->get()
            ->unique('wholesaler_id');

        $storedMarketPrices = $this->anonymizationService
            ->getPublicComparisons($product->id, $user->purchase_size, $filters->wholesalerId)
            ->unique('wholesaler_id')
            ->keyBy('wholesaler_id');

        $wholesalerIds = $userSubmissions->pluck('wholesaler_id')
            ->merge($storedMarketPrices->keys())
            ->unique()
            ->values();

        $rows = [];

        foreach ($wholesalerIds as $wholesalerId) {
            $submission = $userSubmissions->firstWhere('wholesaler_id', $wholesalerId);
            $marketStats = $user->purchase_size
                ? $this->anonymizationService->getComparisonStatsExcludingUser(
                    $product->id,
                    (int) $wholesalerId,
                    $user->purchase_size,
                    $user->id,
                    $periodStart,
                    $periodEnd,
                )
                : null;
            $market = $marketStats ? (object) $marketStats : null;

            $userPrice = $submission ? (float) $submission->price : null;
            $evaluation = ($userPrice !== null && $marketStats !== null)
                ? $this->evaluateRangePosition($userPrice, $marketStats)
                : [
                    'range_position' => null,
                    'range_percent' => null,
                    'difference_from_min_percent' => null,
                    'difference_from_max_percent' => null,
                ];

            $rows[] = [
                'wholesaler_id' => (int) $wholesalerId,
                'wholesaler_name' => $submission?->wholesaler?->name
                    ?? $storedMarketPrices->get($wholesalerId)?->wholesaler?->name
                    ?? 'Onbekend',
                'user_price' => $userPrice,
                'user_unit' => $submission?->unit,
                'market' => $market,
                ...$evaluation,
            ];
        }

        usort($rows, fn ($a, $b) => strcmp($a['wholesaler_name'], $b['wholesaler_name']));

        $purchaseSizeLabel = $user->purchase_size
            ? (AnonymizationService::purchaseSizeLabels()[$user->purchase_size] ?? $user->purchase_size)
            : null;

        return [
            'product' => $product,
            'purchase_size' => $user->purchase_size,
            'purchase_size_label' => $purchaseSizeLabel,
            'rows' => $rows,
        ];
    }

    /**
     * @param  array{min_price: float, max_price: float}  $marketStats
     * @return array{
     *     range_position: 'below'|'within'|'above',
     *     range_percent: float,
     *     difference_from_min_percent: float|null,
     *     difference_from_max_percent: float|null
     * }
     */
    private function evaluateRangePosition(float $userPrice, array $marketStats): array
    {
        $min = (float) $marketStats['min_price'];
        $max = (float) $marketStats['max_price'];

        if ($userPrice < $min * 0.995) {
            $rangePosition = 'below';
        } elseif ($userPrice > $max * 1.005) {
            $rangePosition = 'above';
        } else {
            $rangePosition = 'within';
        }

        if ($max > $min) {
            $rangePercent = round(min(100, max(0, (($userPrice - $min) / ($max - $min)) * 100)), 1);
        } else {
            $rangePercent = 50.0;
        }

        $differenceFromMinPercent = $min > 0
            ? round((($userPrice - $min) / $min) * 100, 1)
            : null;

        $differenceFromMaxPercent = $max > 0
            ? round((($userPrice - $max) / $max) * 100, 1)
            : null;

        return [
            'range_position' => $rangePosition,
            'range_percent' => $rangePercent,
            'difference_from_min_percent' => $differenceFromMinPercent,
            'difference_from_max_percent' => $differenceFromMaxPercent,
        ];
    }
}
