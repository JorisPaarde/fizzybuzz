<?php

namespace Tests\Feature;

use App\Models\PriceSubmission;
use App\Models\Product;
use App\Models\User;
use App\Models\Wholesaler;
use App\Services\AnonymizationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestCompareTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_browse_compare_without_login(): void
    {
        $this->get(route('compare.index'))
            ->assertOk()
            ->assertSee('Prijzen vergelijken')
            ->assertSee('Gratis aanmelden');
    }

    public function test_guest_sees_product_names_but_not_actual_prices(): void
    {
        $others = User::factory()->count(3)->create(['purchase_size' => 'medium']);
        $wholesaler = Wholesaler::query()->create(['name' => 'Sligro', 'slug' => 'sligro']);
        $product = Product::findOrCreateFromName('Tomaten cherry');

        foreach ($others as $other) {
            PriceSubmission::query()->create([
                'user_id' => $other->id,
                'product_id' => $product->id,
                'wholesaler_id' => $wholesaler->id,
                'price' => 11.50,
                'unit' => 'doos',
                'effective_date' => now(),
                'source' => 'manual',
                'status' => PriceSubmission::STATUS_APPROVED,
            ]);
        }

        app(AnonymizationService::class)->aggregate($product->id, $wholesaler->id);

        $this->get(route('compare.show', $product))
            ->assertOk()
            ->assertSee('Tomaten cherry')
            ->assertSee('Sligro')
            ->assertSee('Ontgrendel marktprijzen')
            ->assertSee('leden')
            ->assertDontSee('11,50')
            ->assertDontSee('11.50');
    }

    public function test_authenticated_member_still_sees_full_prices(): void
    {
        $user = User::factory()->create(['purchase_size' => 'medium']);
        $others = User::factory()->count(2)->create(['purchase_size' => 'medium']);
        $wholesaler = Wholesaler::query()->create(['name' => 'Sligro', 'slug' => 'sligro']);
        $product = Product::findOrCreateFromName('Melk vol');

        PriceSubmission::query()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'wholesaler_id' => $wholesaler->id,
            'price' => 1.45,
            'unit' => 'liter',
            'effective_date' => now(),
            'source' => 'manual',
            'status' => PriceSubmission::STATUS_APPROVED,
        ]);

        foreach ($others as $other) {
            PriceSubmission::query()->create([
                'user_id' => $other->id,
                'product_id' => $product->id,
                'wholesaler_id' => $wholesaler->id,
                'price' => 1.10,
                'unit' => 'liter',
                'effective_date' => now(),
                'source' => 'manual',
                'status' => PriceSubmission::STATUS_APPROVED,
            ]);
        }

        PriceSubmission::query()->create([
            'user_id' => User::factory()->create(['purchase_size' => 'medium'])->id,
            'product_id' => $product->id,
            'wholesaler_id' => $wholesaler->id,
            'price' => 1.15,
            'unit' => 'liter',
            'effective_date' => now(),
            'source' => 'manual',
            'status' => PriceSubmission::STATUS_APPROVED,
        ]);

        app(AnonymizationService::class)->aggregate($product->id, $wholesaler->id);

        $this->actingAs($user)
            ->get(route('compare.show', $product))
            ->assertOk()
            ->assertSee('1,45')
            ->assertSee('1,10')
            ->assertDontSee('Ontgrendel marktprijzen');
    }
}
