<?php

namespace Tests\Feature\Inbox;

use App\Models\Agency;
use App\Models\InboxMessage;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InboxTest extends TestCase
{
    use RefreshDatabase;

    private Agency $agency;
    private User $user;
    private SocialAccount $account;

    protected function setUp(): void
    {
        parent::setUp();
        $this->agency = Agency::factory()->create();
        $this->user = User::factory()->create(['agency_id' => $this->agency->id]);
        $this->account = SocialAccount::factory()->create(['agency_id' => $this->agency->id]);
    }

    /** @test */
    public function it_lists_inbox_messages(): void
    {
        InboxMessage::factory()->count(3)->create([
            'agency_id' => $this->agency->id,
            'social_account_id' => $this->account->id,
        ]);
        $response = $this->actingAs($this->user)->get(route('inbox.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_shows_inbox_message(): void
    {
        $message = InboxMessage::factory()->create([
            'agency_id' => $this->agency->id,
            'social_account_id' => $this->account->id,
        ]);
        $response = $this->actingAs($this->user)->get(route('inbox.show', $message));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_triages_message(): void
    {
        $message = InboxMessage::factory()->create([
            'agency_id' => $this->agency->id,
            'social_account_id' => $this->account->id,
        ]);
        $response = $this->actingAs($this->user)->post(route('inbox.triage', $message), [
            'action' => 'reply',
        ]);
        $response->assertRedirect();
    }

    /** @test */
    public function it_replies_to_message(): void
    {
        $message = InboxMessage::factory()->create([
            'agency_id' => $this->agency->id,
            'social_account_id' => $this->account->id,
        ]);
        $response = $this->actingAs($this->user)->post(route('inbox.reply', $message), [
            'reply' => 'Thank you for your message',
        ]);
        $response->assertRedirect();
    }

    /** @test */
    public function it_prevents_unauthorized_access(): void
    {
        $otherAgency = Agency::factory()->create();
        $otherAccount = SocialAccount::factory()->create(['agency_id' => $otherAgency->id]);
        $message = InboxMessage::factory()->create([
            'agency_id' => $otherAgency->id,
            'social_account_id' => $otherAccount->id,
        ]);
        $response = $this->actingAs($this->user)->get(route('inbox.show', $message));
        $response->assertForbidden();
    }
}
