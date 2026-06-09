<?php

namespace Tests\Feature;

use App\Models\AggregatedPrice;
use App\Models\PriceSubmission;
use App\Models\Product;
use App\Models\User;
use App\Models\Wholesaler;
use App\Services\AnonymizationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PriceComparisonTest extends TestCase
{
    use RefreshDatabase;

    public function test_compare_index_requires_auth(): void
    {
        $this->get(route('compare.index'))->assertRedirect(route('login'));
    }

    public function test_user_can_search_and_view_product_comparison(): void
    {
        $user = User::factory()->create();
        $otherUsers = User::factory()->count(2)->create();
        $wholesaler = Wholesaler::query()->create(['name' => 'Sligro', 'slug' => 'sligro']);
        $product = Product::findOrCreateFromName('Tomaten cherry');

        PriceSubmission::query()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'wholesaler_id' => $wholesaler->id,
            'price' => 14.80,
            'unit' => 'doos',
            'effective_date' => now(),
            'source' => 'manual',
            'status' => PriceSubmission::STATUS_APPROVED,
        ]);

        foreach ([$otherUsers[0], $otherUsers[1], User::factory()->create()] as $other) {
            PriceSubmission::query()->create([
                'user_id' => $other->id,
                'product_id' => $product->id,
                'wholesaler_id' => $wholesaler->id,
                'price' => 11.00,
                'unit' => 'doos',
                'effective_date' => now(),
                'source' => 'manual',
                'status' => PriceSubmission::STATUS_APPROVED,
            ]);
        }

        app(AnonymizationService::class)->aggregate($product->id, $wholesaler->id);

        $this->actingAs($user)
            ->get(route('compare.index', ['q' => 'Tomaten']))
            ->assertOk()
            ->assertSee('Tomaten cherry');

        $this->actingAs($user)
            ->get(route('compare.show', $product))
            ->assertOk()
            ->assertSee('Sligro')
            ->assertSee('14,80')
            ->assertSee('boven gemiddelde')
            ->assertSee('leden');
    }

    public function test_market_data_hidden_below_minimum_datapoints(): void
    {
        $user = User::factory()->create();
        $wholesaler = Wholesaler::query()->create(['name' => 'Hanos', 'slug' => 'hanos']);
        $product = Product::findOrCreateFromName('Melk');

        PriceSubmission::query()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'wholesaler_id' => $wholesaler->id,
            'price' => 2.50,
            'unit' => 'liter',
            'effective_date' => now(),
            'source' => 'manual',
            'status' => PriceSubmission::STATUS_APPROVED,
        ]);

        AggregatedPrice::query()->create([
            'product_id' => $product->id,
            'wholesaler_id' => $wholesaler->id,
            'avg_price' => 2.00,
            'median_price' => 2.00,
            'min_price' => 1.80,
            'max_price' => 2.20,
            'datapoint_count' => 2,
            'period_start' => now()->subDays(90),
            'period_end' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('compare.show', $product))
            ->assertOk()
            ->assertSee('Nog onvoldoende data')
            ->assertDontSee('2,00');
    }
}
