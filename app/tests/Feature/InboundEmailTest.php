<?php

namespace Tests\Feature;

use App\Models\PriceImport;
use App\Models\User;
use App\Services\InboundEmailService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class InboundEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_inbound_email_webhook_creates_review_import_for_known_user(): void
    {
        $user = User::factory()->create(['email' => 'cafe@example.com']);

        $import = PriceImport::query()->create([
            'user_id' => $user->id,
            'source' => PriceImport::SOURCE_EMAIL,
            'status' => PriceImport::STATUS_REVIEW,
            'extracted_items' => [['product_name' => 'Melk', 'price' => 1.2, 'unit' => 'liter']],
        ]);

        $file = UploadedFile::fake()->create('factuur.pdf', 100, 'application/pdf');

        $this->mock(InboundEmailService::class, function ($mock) use ($user, $import, $file): void {
            $mock->shouldReceive('resolveUserFromSender')
                ->once()
                ->andReturn($user);
            $mock->shouldReceive('attachmentsFromMailgunRequest')
                ->once()
                ->andReturn([$file]);
            $mock->shouldReceive('process')
                ->once()
                ->andReturn($import);
        });

        $this->post('/webhooks/inbound-email', [
            'sender' => 'Café <cafe@example.com>',
            'attachment-count' => 1,
        ])->assertOk();
    }

    public function test_inbound_email_rejects_unknown_sender(): void
    {
        $this->mock(InboundEmailService::class, function ($mock): void {
            $mock->shouldReceive('resolveUserFromSender')
                ->once()
                ->andReturn(null);
        });

        $this->post('/webhooks/inbound-email', [
            'sender' => 'unknown@example.com',
            'attachment-count' => 0,
        ])->assertStatus(406);
    }

    public function test_service_extracts_sender_email(): void
    {
        $service = app(InboundEmailService::class);

        $this->assertSame('cafe@example.com', $service->extractEmailAddress('Café <cafe@example.com>'));
        $this->assertSame('test@horeca.nl', $service->extractEmailAddress('test@horeca.nl'));
    }
}
