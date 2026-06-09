<?php

namespace Tests\Feature;

use App\Models\PriceSubmission;
use App\Models\Product;
use App\Models\User;
use App\Models\Wholesaler;
use App\Services\AnonymizationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardInsightTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_products_above_market_range(): void
    {
        $user = User::factory()->create(['purchase_size' => 'medium']);
        $others = User::factory()->count(2)->create(['purchase_size' => 'medium']);
        $sligro = Wholesaler::query()->create(['name' => 'Sligro', 'slug' => 'sligro']);
        $hanos = Wholesaler::query()->create(['name' => 'Hanos', 'slug' => 'hanos']);
        $tomaten = Product::findOrCreateFromName('Tomaten cherry');
        $melk = Product::findOrCreateFromName('Melk');

        PriceSubmission::query()->create([
            'user_id' => $user->id,
            'product_id' => $tomaten->id,
            'wholesaler_id' => $sligro->id,
            'price' => 15.00,
            'unit' => 'doos',
            'effective_date' => now(),
            'source' => 'manual',
            'status' => PriceSubmission::STATUS_APPROVED,
        ]);

        PriceSubmission::query()->create([
            'user_id' => $user->id,
            'product_id' => $melk->id,
            'wholesaler_id' => $hanos->id,
            'price' => 1.20,
            'unit' => 'liter',
            'effective_date' => now(),
            'source' => 'manual',
            'status' => PriceSubmission::STATUS_APPROVED,
        ]);

        foreach ([10.00, 11.00] as $index => $price) {
            PriceSubmission::query()->create([
                'user_id' => $others[$index]->id,
                'product_id' => $tomaten->id,
                'wholesaler_id' => $sligro->id,
                'price' => $price,
                'unit' => 'doos',
                'effective_date' => now(),
                'source' => 'manual',
                'status' => PriceSubmission::STATUS_APPROVED,
            ]);
        }

        PriceSubmission::query()->create([
            'user_id' => User::factory()->create(['purchase_size' => 'medium'])->id,
            'product_id' => $tomaten->id,
            'wholesaler_id' => $sligro->id,
            'price' => 10.50,
            'unit' => 'doos',
            'effective_date' => now(),
            'source' => 'manual',
            'status' => PriceSubmission::STATUS_APPROVED,
        ]);

        foreach (User::factory()->count(3)->create(['purchase_size' => 'medium']) as $other) {
            PriceSubmission::query()->create([
                'user_id' => $other->id,
                'product_id' => $melk->id,
                'wholesaler_id' => $hanos->id,
                'price' => 0.90,
                'unit' => 'liter',
                'effective_date' => now(),
                'source' => 'manual',
                'status' => PriceSubmission::STATUS_APPROVED,
            ]);
        }

        app(AnonymizationService::class)->aggregate($tomaten->id, $sligro->id);
        app(AnonymizationService::class)->aggregate($melk->id, $hanos->id);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Marktinzicht')
            ->assertSee('Boven marktrange')
            ->assertSee('Tomaten cherry')
            ->assertSee('boven markt')
            ->assertSee('Sligro');
    }

    public function test_dashboard_filters_by_wholesaler(): void
    {
        $user = User::factory()->create(['purchase_size' => 'medium']);
        $others = User::factory()->count(2)->create(['purchase_size' => 'medium']);
        $sligro = Wholesaler::query()->create(['name' => 'Sligro', 'slug' => 'sligro']);
        $hanos = Wholesaler::query()->create(['name' => 'Hanos', 'slug' => 'hanos']);
        $product = Product::findOrCreateFromName('Cola');

        foreach ([$sligro, $hanos] as $wholesaler) {
            PriceSubmission::query()->create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'wholesaler_id' => $wholesaler->id,
                'price' => 20.00,
                'unit' => 'krat',
                'effective_date' => now(),
                'source' => 'manual',
                'status' => PriceSubmission::STATUS_APPROVED,
            ]);
        }

        foreach ($others as $other) {
            foreach ([$sligro, $hanos] as $wholesaler) {
                PriceSubmission::query()->create([
                    'user_id' => $other->id,
                    'product_id' => $product->id,
                    'wholesaler_id' => $wholesaler->id,
                    'price' => 12.00,
                    'unit' => 'krat',
                    'effective_date' => now(),
                    'source' => 'manual',
                    'status' => PriceSubmission::STATUS_APPROVED,
                ]);
            }
        }

        PriceSubmission::query()->create([
            'user_id' => User::factory()->create(['purchase_size' => 'medium'])->id,
            'product_id' => $product->id,
            'wholesaler_id' => $sligro->id,
            'price' => 13.00,
            'unit' => 'krat',
            'effective_date' => now(),
            'source' => 'manual',
            'status' => PriceSubmission::STATUS_APPROVED,
        ]);

        PriceSubmission::query()->create([
            'user_id' => User::factory()->create(['purchase_size' => 'medium'])->id,
            'product_id' => $product->id,
            'wholesaler_id' => $hanos->id,
            'price' => 13.00,
            'unit' => 'krat',
            'effective_date' => now(),
            'source' => 'manual',
            'status' => PriceSubmission::STATUS_APPROVED,
        ]);

        app(AnonymizationService::class)->aggregate($product->id, $sligro->id);
        app(AnonymizationService::class)->aggregate($product->id, $hanos->id);

        $this->actingAs($user)
            ->get(route('dashboard', ['wholesaler_id' => $sligro->id]))
            ->assertOk()
            ->assertSee('Cola')
            ->assertSee('Sligro');
    }
}
