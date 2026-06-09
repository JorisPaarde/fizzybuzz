<?php

namespace Tests\Feature;

use App\Enums\AddedVia;
use App\Models\PriceImport;
use App\Models\Product;
use App\Models\User;
use App\Models\UserProduct;
use App\Models\Wholesaler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MyProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_upload_confirm_adds_products_to_my_products_list(): void
    {
        $user = User::factory()->create(['purchase_size' => 'medium']);
        $wholesaler = Wholesaler::query()->create(['name' => 'Sligro', 'slug' => 'sligro']);

        $import = PriceImport::query()->create([
            'user_id' => $user->id,
            'source' => PriceImport::SOURCE_MANUAL,
            'status' => PriceImport::STATUS_REVIEW,
            'extracted_items' => [],
        ]);

        $this->actingAs($user)
            ->post(route('prices.import.confirm', $import), [
                'wholesaler_id' => $wholesaler->id,
                'effective_date' => '2026-06-01',
                'items' => [
                    [
                        'product_name' => 'Tomaten cherry',
                        'price' => 12.5,
                        'unit' => 'doos',
                        'quantity_per_unit' => '5 kg',
                        'specification' => null,
                    ],
                ],
            ])
            ->assertRedirect(route('prices.index'));

        $product = Product::query()->where('slug', 'tomaten-cherry')->first();
        $this->assertNotNull($product);

        $this->assertDatabaseHas('user_products', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'added_via' => AddedVia::Upload->value,
        ]);
    }

    public function test_user_can_manually_add_and_remove_product(): void
    {
        $user = User::factory()->create();
        $product = Product::query()->create([
            'name' => 'Espresso bonen',
            'slug' => 'espresso-bonen',
            'standard_unit' => 'kg',
        ]);

        $this->actingAs($user)
            ->post(route('my-products.store'), ['product_id' => $product->id])
            ->assertRedirect();

        $this->assertDatabaseHas('user_products', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'added_via' => AddedVia::Manual->value,
        ]);

        $this->actingAs($user)
            ->delete(route('my-products.destroy', $product))
            ->assertRedirect();

        $this->assertDatabaseMissing('user_products', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_my_products_index_lists_tracked_products(): void
    {
        $user = User::factory()->create(['purchase_size' => 'medium']);
        $product = Product::query()->create([
            'name' => 'Volle melk',
            'slug' => 'volle-melk',
            'standard_unit' => 'liter',
        ]);

        UserProduct::query()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'added_via' => AddedVia::Manual,
        ]);

        $this->actingAs($user)
            ->get(route('my-products.index'))
            ->assertOk()
            ->assertSee('Volle melk')
            ->assertSee('Mijn producten');
    }

    public function test_user_cannot_remove_another_users_product(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $product = Product::query()->create([
            'name' => 'Boter',
            'slug' => 'boter',
            'standard_unit' => 'kg',
        ]);

        UserProduct::query()->create([
            'user_id' => $owner->id,
            'product_id' => $product->id,
            'added_via' => AddedVia::Manual,
        ]);

        $this->actingAs($other)
            ->delete(route('my-products.destroy', $product))
            ->assertNotFound();
    }

    public function test_search_excludes_products_already_on_list(): void
    {
        $user = User::factory()->create();
        $onList = Product::query()->create(['name' => 'Tomaten', 'slug' => 'tomaten', 'standard_unit' => 'kg']);
        $available = Product::query()->create(['name' => 'Tomaten passata', 'slug' => 'tomaten-passata', 'standard_unit' => 'kg']);

        UserProduct::query()->create([
            'user_id' => $user->id,
            'product_id' => $onList->id,
            'added_via' => AddedVia::Manual,
        ]);

        $results = app(\App\Services\UserProductService::class)->searchProductsToAdd($user, 'passata');

        $this->assertCount(1, $results);
        $this->assertTrue($results->first()->is($available));
        $this->assertFalse($results->contains(fn ($p) => $p->is($onList)));
    }
}
