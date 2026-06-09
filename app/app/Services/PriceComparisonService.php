<?php

namespace App\Services;

use App\Models\AggregatedPrice;
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
                        ->where('datapoint_count', '>=', AnonymizationService::MIN_DATAPOINTS));
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
     *     rows: array<int, array{
     *         wholesaler_id: int,
     *         wholesaler_name: string,
     *         user_price: float|null,
     *         user_unit: string|null,
     *         market: AggregatedPrice|null,
     *         position: 'below'|'above'|'at'|null,
     *         difference_percent: float|null
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

        $marketPrices = $this->anonymizationService
            ->getPublicComparisons($product->id)
            ->unique('wholesaler_id')
            ->keyBy('wholesaler_id');

        $wholesalerIds = $userSubmissions->pluck('wholesaler_id')
            ->merge($marketPrices->keys())
            ->unique()
            ->values();

        $rows = [];

        foreach ($wholesalerIds as $wholesalerId) {
            $submission = $userSubmissions->firstWhere('wholesaler_id', $wholesalerId);
            $market = $marketPrices->get($wholesalerId);

            $userPrice = $submission ? (float) $submission->price : null;
            $position = null;
            $differencePercent = null;

            if ($userPrice !== null && $market !== null) {
                $avg = (float) $market->avg_price;
                if ($userPrice < $avg * 0.995) {
                    $position = 'below';
                } elseif ($userPrice > $avg * 1.005) {
                    $position = 'above';
                } else {
                    $position = 'at';
                }
                $differencePercent = $avg > 0
                    ? round((($userPrice - $avg) / $avg) * 100, 1)
                    : null;
            }

            $rows[] = [
                'wholesaler_id' => (int) $wholesalerId,
                'wholesaler_name' => $submission?->wholesaler?->name
                    ?? $market?->wholesaler?->name
                    ?? 'Onbekend',
                'user_price' => $userPrice,
                'user_unit' => $submission?->unit,
                'market' => $market,
                'position' => $position,
                'difference_percent' => $differencePercent,
            ];
        }

        usort($rows, fn ($a, $b) => strcmp($a['wholesaler_name'], $b['wholesaler_name']));

        return [
            'product' => $product,
            'rows' => $rows,
        ];
    }
}
