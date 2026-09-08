<?php

namespace Tests\Feature\Inbox;

use App\Models\Agency;
use App\Models\InboxMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InboxTest extends TestCase
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
    public function it_lists_inbox_messages(): void
    {
        InboxMessage::factory()->count(3)->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->get(route('inbox.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_shows_a_message(): void
    {
        $message = InboxMessage::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->get(route('inbox.show', $message));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_triages_a_message(): void
    {
        $message = InboxMessage::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->post(route('inbox.triage', $message), [
            'category' => 'lead',
            'priority' => 'high',
        ]);
        $response->assertRedirect();
    }

    /** @test */
    public function it_prevents_access_to_other_agency_messages(): void
    {
        $otherAgency = Agency::factory()->create();
        $message = InboxMessage::factory()->create(['agency_id' => $otherAgency->id]);
        $response = $this->actingAs($this->user)->get(route('inbox.show', $message));
        $response->assertForbidden();
    }

    /** @test */
    public function it_requires_auth(): void
    {
        $response = $this->get(route('inbox.index'));
        $response->assertRedirect(route('login'));
    }
}
