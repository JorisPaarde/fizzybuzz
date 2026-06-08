<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Wholesaler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberWholesalerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_link_and_unlink_wholesaler(): void
    {
        $user = User::factory()->create();
        $wholesaler = Wholesaler::query()->create(['name' => 'Sligro', 'slug' => 'sligro']);

        $this->actingAs($user)
            ->post(route('wholesalers.store'), ['wholesaler_id' => $wholesaler->id])
            ->assertRedirect();

        $this->assertTrue($user->wholesalers()->whereKey($wholesaler->id)->exists());

        $this->actingAs($user)
            ->delete(route('wholesalers.destroy', $wholesaler))
            ->assertRedirect();

        $this->assertFalse($user->fresh()->wholesalers()->whereKey($wholesaler->id)->exists());
    }

    public function test_user_can_create_new_wholesaler_by_name(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('wholesalers.store'), ['wholesaler_name' => 'Lokale Leverancier'])
            ->assertRedirect();

        $this->assertDatabaseHas('wholesalers', ['name' => 'Lokale Leverancier']);
        $this->assertTrue($user->fresh()->wholesalers()->where('name', 'Lokale Leverancier')->exists());
    }
}
