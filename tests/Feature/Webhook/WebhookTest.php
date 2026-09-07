<?php

namespace Tests\Feature\Webhook;

use App\Models\Agency;
use App\Models\User;
use App\Models\Webhook;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebhookTest extends TestCase
{
    use RefreshDatabase;

    private Agency $agency;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->agency = Agency::factory()->create();
        $this->user = User::factory()->create(['agency_id' => $this->agency->id]);
    }

    /** @test */
    public function it_lists_webhooks(): void
    {
        Webhook::factory()->count(3)->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->get(route('webhooks.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_creates_webhook(): void
    {
        $response = $this->actingAs($this->user)->post(route('webhooks.store'), [
            'name' => 'Test Webhook',
            'url' => 'https://example.com/webhook',
            'events' => ['post_created'],
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('webhooks', ['name' => 'Test Webhook']);
    }

    /** @test */
    public function it_shows_webhook(): void
    {
        $webhook = Webhook::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->get(route('webhooks.show', $webhook));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_updates_webhook(): void
    {
        $webhook = Webhook::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->put(route('webhooks.update', $webhook), [
            'name' => 'Updated Webhook',
        ]);
        $response->assertRedirect();
    }

    /** @test */
    public function it_deletes_webhook(): void
    {
        $webhook = Webhook::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->delete(route('webhooks.destroy', $webhook));
        $response->assertRedirect();
        $this->assertSoftDeleted('webhooks', ['id' => $webhook->id]);
    }

    /** @test */
    public function it_prevents_unauthorized_access(): void
    {
        $otherAgency = Agency::factory()->create();
        $webhook = Webhook::factory()->create(['agency_id' => $otherAgency->id]);
        $response = $this->actingAs($this->user)->get(route('webhooks.show', $webhook));
        $response->assertForbidden();
    }
}
