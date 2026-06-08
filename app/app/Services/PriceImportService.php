<?php

namespace App\Services;

use App\Models\PriceImport;
use App\Models\PriceSubmission;
use App\Models\Product;
use App\Models\User;
use App\Models\Wholesaler;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PriceImportService
{
    public function __construct(
        private readonly AnonymizationService $anonymizationService,
    ) {}

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    public function confirm(PriceImport $import, User $user, int $wholesalerId, string $effectiveDate, array $items): int
    {
        if ($import->user_id !== $user->id) {
            abort(403);
        }

        if ($import->status === PriceImport::STATUS_CONFIRMED) {
            abort(422, 'Deze import is al bevestigd.');
        }

        return DB::transaction(function () use ($import, $user, $wholesalerId, $effectiveDate, $items) {
            $saved = 0;
            $productIds = [];

            $user->wholesalers()->syncWithoutDetaching([$wholesalerId]);

            foreach ($items as $item) {
                if (blank($item['product_name'] ?? null) || ! is_numeric($item['price'] ?? null)) {
                    continue;
                }

                $product = Product::findOrCreateFromName((string) $item['product_name']);
                $productIds[] = $product->id;

                PriceSubmission::query()->create([
                    'user_id' => $import->user_id,
                    'price_import_id' => $import->id,
                    'product_id' => $product->id,
                    'wholesaler_id' => $wholesalerId,
                    'price' => round((float) $item['price'], 2),
                    'unit' => (string) ($item['unit'] ?? 'stuk'),
                    'quantity_per_unit' => $item['quantity_per_unit'] ?? null,
                    'specification' => $item['specification'] ?? null,
                    'effective_date' => $effectiveDate,
                    'source' => $import->source,
                    'status' => PriceSubmission::STATUS_APPROVED,
                ]);

                $saved++;
            }

            $import->update([
                'wholesaler_id' => $wholesalerId,
                'effective_date' => $effectiveDate,
                'extracted_items' => $items,
                'status' => PriceImport::STATUS_CONFIRMED,
            ]);

            foreach (array_unique($productIds) as $productId) {
                $this->anonymizationService->aggregate($productId, $wholesalerId);
            }

            return $saved;
        });
    }

    public function resolveWholesalerId(?int $wholesalerId, ?string $newName): int
    {
        if (filled($newName)) {
            return Wholesaler::findOrCreateFromName($newName)->id;
        }

        if ($wholesalerId) {
            return $wholesalerId;
        }

        abort(422, 'Kies een groothandel of voer een nieuwe naam in.');
    }

    public function guessWholesalerId(?string $name): ?int
    {
        if (blank($name)) {
            return null;
        }

        $wholesaler = Wholesaler::query()
            ->where('slug', \Illuminate\Support\Str::slug($name))
            ->orWhere('name', 'like', '%'.$name.'%')
            ->first();

        return $wholesaler?->id;
    }

    public function parseEffectiveDate(?string $date): string
    {
        if (blank($date)) {
            return now()->toDateString();
        }

        try {
            return Carbon::parse($date)->toDateString();
        } catch (\Throwable) {
            return now()->toDateString();
        }
    }
}
