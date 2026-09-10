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

    public function test_it_lists_webhooks(): void
    {
        Webhook::factory()->count(3)->create(['agency_id' => $this->agency->id]);

        $response = $this->actingAs($this->user)->get(route('webhooks.index'));

        $response->assertOk();
        $response->assertViewIs('webhooks.index');
        $response->assertViewHas('webhooks');
    }

    public function test_it_creates_a_webhook(): void
    {
        $response = $this->actingAs($this->user)->post(route('webhooks.store'), [
            'name' => 'Test Webhook',
            'url' => 'https://example.com/webhook',
            'events' => ['post.published'],
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('webhooks', [
            'name' => 'Test Webhook',
            'agency_id' => $this->agency->id,
        ]);
    }

    public function test_it_validates_webhook_creation(): void
    {
        $response = $this->actingAs($this->user)->post(route('webhooks.store'), []);

        $response->assertSessionHasErrors(['name', 'url', 'events']);
    }

    public function test_it_shows_a_webhook(): void
    {
        $webhook = Webhook::factory()->create(['agency_id' => $this->agency->id]);

        $response = $this->actingAs($this->user)->get(route('webhooks.show', $webhook));

        $response->assertOk();
        $response->assertViewIs('webhooks.show');
        $response->assertViewHas('webhook');
    }

    public function test_it_prevents_showing_other_agency_webhooks(): void
    {
        $otherAgency = Agency::factory()->create();
        $webhook = Webhook::factory()->create(['agency_id' => $otherAgency->id]);

        $response = $this->actingAs($this->user)->get(route('webhooks.show', $webhook));

        $response->assertForbidden();
    }

    public function test_it_edits_a_webhook(): void
    {
        $webhook = Webhook::factory()->create(['agency_id' => $this->agency->id]);

        $response = $this->actingAs($this->user)->get(route('webhooks.edit', $webhook));

        $response->assertOk();
        $response->assertViewIs('webhooks.edit');
    }

    public function test_it_updates_a_webhook(): void
    {
        $webhook = Webhook::factory()->create(['agency_id' => $this->agency->id]);

        $response = $this->actingAs($this->user)->put(route('webhooks.update', $webhook), [
            'name' => 'Updated Webhook',
            'url' => 'https://example.com/updated',
            'events' => ['post.published'],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('webhooks', [
            'id' => $webhook->id,
            'name' => 'Updated Webhook',
        ]);
    }

    public function test_it_deletes_a_webhook(): void
    {
        $webhook = Webhook::factory()->create(['agency_id' => $this->agency->id]);

        $response = $this->actingAs($this->user)->delete(route('webhooks.destroy', $webhook));

        $response->assertRedirect(route('webhooks.index'));
        $this->assertSoftDeleted('webhooks', ['id' => $webhook->id]);
    }

    public function test_it_requires_auth(): void
    {
        $response = $this->get(route('webhooks.index'));

        $response->assertRedirect(route('login'));
    }
}
