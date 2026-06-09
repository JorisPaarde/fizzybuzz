<?php

namespace App\Services;

use App\Enums\AddedVia;
use App\Models\PriceSubmission;
use App\Models\Product;
use App\Models\User;
use App\Models\UserProduct;
use Illuminate\Support\Collection;

class UserProductService
{
    public function __construct(
        private readonly PriceComparisonService $priceComparisonService,
    ) {}

    public function add(User $user, Product $product, AddedVia $via): UserProduct
    {
        return UserProduct::query()->firstOrCreate(
            [
                'user_id' => $user->id,
                'product_id' => $product->id,
            ],
            ['added_via' => $via],
        );
    }

    /**
     * @param  array<int, int>  $productIds
     */
    public function addFromUpload(User $user, array $productIds): void
    {
        foreach (array_unique($productIds) as $productId) {
            $this->add($user, Product::query()->findOrFail($productId), AddedVia::Upload);
        }
    }

    public function remove(User $user, Product $product): void
    {
        UserProduct::query()
            ->where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->delete();
    }

    /**
     * @return Collection<int, Product>
     */
    public function searchProductsToAdd(User $user, string $query, int $limit = 10): Collection
    {
        $trackedIds = UserProduct::query()
            ->where('user_id', $user->id)
            ->pluck('product_id');

        return Product::query()
            ->when($trackedIds->isNotEmpty(), fn ($q) => $q->whereNotIn('id', $trackedIds))
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', '%'.$query.'%')
                    ->orWhere('slug', 'like', '%'.str($query)->slug().'%');
            })
            ->orderBy('name')
            ->limit($limit)
            ->get();
    }

    /**
     * @return array<int, array{
     *     product: Product,
     *     added_via: AddedVia,
     *     latest_submission: PriceSubmission|null,
     *     market_min: float|null,
     *     position: 'above'|'within'|'below'|'unknown',
     *     position_label: string
     * }>
     */
    public function listRows(User $user): array
    {
        $entries = UserProduct::query()
            ->with('product')
            ->where('user_id', $user->id)
            ->get();

        $rows = [];

        foreach ($entries as $entry) {
            $product = $entry->product;
            if ($product === null) {
                continue;
            }

            $latest = PriceSubmission::query()
                ->with('wholesaler')
                ->where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->where('status', PriceSubmission::STATUS_APPROVED)
                ->orderByDesc('effective_date')
                ->orderByDesc('id')
                ->first();

            $summary = $this->summarizeForList($user, $product);

            $rows[] = [
                'product' => $product,
                'added_via' => $entry->added_via,
                'latest_submission' => $latest,
                ...$summary,
            ];
        }

        usort($rows, function (array $a, array $b): int {
            $savingsA = $this->savingsScore($a);
            $savingsB = $this->savingsScore($b);

            if ($savingsA !== $savingsB) {
                return $savingsB <=> $savingsA;
            }

            return strcasecmp($a['product']->name, $b['product']->name);
        });

        return $rows;
    }

    /**
     * @return array{market_min: float|null, position: 'above'|'within'|'below'|'unknown', position_label: string}
     */
    private function summarizeForList(User $user, Product $product): array
    {
        $comparison = $this->priceComparisonService->compareProductForUser($user, $product);

        $marketMins = [];
        $worstPosition = 'unknown';
        $priority = ['above' => 3, 'within' => 2, 'below' => 1, 'unknown' => 0];

        foreach ($comparison['rows'] as $row) {
            if ($row['market'] !== null) {
                $marketMins[] = (float) $row['market']->min_price;
            }

            $position = $row['range_position'] ?? 'unknown';
            if (($priority[$position] ?? 0) > ($priority[$worstPosition] ?? 0)) {
                $worstPosition = $position;
            }
        }

        $positionLabel = match ($worstPosition) {
            'above' => 'Boven markt',
            'within' => 'Binnen markt',
            'below' => 'Onder markt',
            default => $user->purchase_size ? 'Nog geen vergelijking' : 'Stel inkoopomvang in',
        };

        return [
            'market_min' => $marketMins !== [] ? min($marketMins) : null,
            'position' => $worstPosition,
            'position_label' => $positionLabel,
        ];
    }

    /**
     * @param  array{position: string, latest_submission: PriceSubmission|null, market_min: float|null}  $row
     */
    private function savingsScore(array $row): float
    {
        if ($row['position'] !== 'above' || $row['latest_submission'] === null || $row['market_min'] === null) {
            return 0.0;
        }

        return max(0.0, (float) $row['latest_submission']->price - $row['market_min']);
    }
}
