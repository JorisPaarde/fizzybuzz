<?php

namespace Tests\Feature;

use App\Models\PriceImport;
use App\Models\User;
use App\Models\Wholesaler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PriceUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_manual_upload_goes_to_review_and_can_be_confirmed(): void
    {
        $user = User::factory()->create([
            'business_name' => 'Café Test',
            'business_type' => 'cafe',
            'region' => 'Utrecht',
        ]);

        $wholesaler = Wholesaler::query()->create([
            'name' => 'Sligro',
            'slug' => 'sligro',
            'is_verified' => true,
        ]);

        $this->actingAs($user)
            ->post(route('prices.manual.store'), [
                'wholesaler_id' => $wholesaler->id,
                'effective_date' => '2026-06-01',
                'items' => [
                    [
                        'product_name' => 'Tomaten cherry',
                        'price' => 12.5,
                        'unit' => 'doos',
                        'quantity_per_unit' => '5 kg',
                    ],
                ],
            ])
            ->assertRedirect();

        $import = PriceImport::query()->first();
        $this->assertNotNull($import);
        $this->assertSame(PriceImport::SOURCE_MANUAL, $import->source);

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

        $this->assertDatabaseHas('price_submissions', [
            'user_id' => $user->id,
            'wholesaler_id' => $wholesaler->id,
            'price' => 12.5,
            'unit' => 'doos',
            'status' => 'approved',
        ]);

        $this->assertTrue($user->fresh()->wholesalers()->whereKey($wholesaler->id)->exists());
    }

    public function test_user_can_edit_own_price_submission(): void
    {
        $user = User::factory()->create();
        $wholesaler = Wholesaler::query()->create(['name' => 'Bidfood', 'slug' => 'bidfood']);
        $product = \App\Models\Product::query()->create(['name' => 'Olijfolie', 'slug' => 'olijfolie']);

        $submission = \App\Models\PriceSubmission::query()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'wholesaler_id' => $wholesaler->id,
            'price' => 30,
            'unit' => 'liter',
            'effective_date' => '2026-06-01',
            'source' => 'manual',
            'status' => 'pending',
        ]);

        $this->actingAs($user)
            ->put(route('prices.update', $submission), [
                'product_name' => 'Olijfolie extra vierge',
                'wholesaler_id' => $wholesaler->id,
                'price' => 28.5,
                'unit' => 'liter',
                'effective_date' => '2026-06-01',
            ])
            ->assertRedirect(route('prices.index'));

        $this->assertDatabaseHas('price_submissions', [
            'id' => $submission->id,
            'price' => 28.5,
        ]);
    }

    public function test_photo_upload_uses_extraction_service_and_opens_review(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();
        Wholesaler::query()->create(['name' => 'Hanos', 'slug' => 'hanos', 'is_verified' => true]);

        $this->mock(\App\Services\PriceExtractionService::class, function ($mock): void {
            $mock->shouldReceive('extractFromUpload')
                ->once()
                ->andReturn([
                    'items' => [
                        [
                            'product_name' => 'Koffiebonen',
                            'specification' => '1 kg',
                            'price' => 18.9,
                            'unit' => 'zak',
                            'quantity_per_unit' => '1 kg',
                        ],
                    ],
                    'wholesaler_guess' => 'Hanos',
                    'document_date' => '2026-05-15',
                ]);
        });

        $file = UploadedFile::fake()->create('factuur.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($user)
            ->post(route('prices.import.store'), ['file' => $file]);

        $import = PriceImport::query()->first();
        $this->assertNotNull($import);
        $response->assertRedirect(route('prices.import.review', $import));
        $this->assertSame(PriceImport::SOURCE_PHOTO, $import->source);
        $this->assertCount(1, $import->extracted_items);
    }
}
