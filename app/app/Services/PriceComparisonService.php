<?php

namespace App\Services;

use App\Models\PriceSubmission;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Collection;

class PriceComparisonService
{
    public function __construct(
        private readonly AnonymizationService $anonymizationService,
    ) {}

    /**
     * Zoek producten op naam — eigen prijzen en/of producten met marktdata.
     */
    public function searchProducts(User $user, ?string $query = null, int $limit = 20): Collection
    {
        $builder = Product::query()
            ->where(function ($q) use ($user) {
                $q->whereHas('priceSubmissions', fn ($s) => $s
                    ->where('user_id', $user->id)
                    ->where('status', PriceSubmission::STATUS_APPROVED))
                    ->orWhereHas('aggregatedPrices', fn ($a) => $a
                        ->where('datapoint_count', '>=', AnonymizationService::MIN_DATAPOINTS)
                        ->when(
                            $user->purchase_size,
                            fn ($query) => $query->where('purchase_size', $user->purchase_size)
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
     *         market: AggregatedPrice|null,
     *         range_position: 'below'|'within'|'above'|null,
     *         range_percent: float|null,
     *         difference_from_min_percent: float|null,
     *         difference_from_max_percent: float|null
     *     }>
     * }
     */
    public function compareProductForUser(User $user, Product $product): array
    {
        $userSubmissions = PriceSubmission::query()
            ->with('wholesaler')
            ->where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->where('status', PriceSubmission::STATUS_APPROVED)
            ->orderByDesc('effective_date')
            ->get()
            ->unique('wholesaler_id');

        $storedMarketPrices = $this->anonymizationService
            ->getPublicComparisons($product->id, $user->purchase_size)
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
                )
                : null;
            $market = $marketStats ? (object) $marketStats : null;

            $userPrice = $submission ? (float) $submission->price : null;
            $rangePosition = null;
            $rangePercent = null;
            $differenceFromMinPercent = null;
            $differenceFromMaxPercent = null;

            if ($userPrice !== null && $marketStats !== null) {
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

                if ($min > 0) {
                    $differenceFromMinPercent = round((($userPrice - $min) / $min) * 100, 1);
                }

                if ($max > 0) {
                    $differenceFromMaxPercent = round((($userPrice - $max) / $max) * 100, 1);
                }
            }

            $rows[] = [
                'wholesaler_id' => (int) $wholesalerId,
                'wholesaler_name' => $submission?->wholesaler?->name
                    ?? $storedMarketPrices->get($wholesalerId)?->wholesaler?->name
                    ?? 'Onbekend',
                'user_price' => $userPrice,
                'user_unit' => $submission?->unit,
                'market' => $market,
                'range_position' => $rangePosition,
                'range_percent' => $rangePercent,
                'difference_from_min_percent' => $differenceFromMinPercent,
                'difference_from_max_percent' => $differenceFromMaxPercent,
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
}
