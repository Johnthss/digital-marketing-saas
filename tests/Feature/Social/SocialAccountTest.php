<?php

namespace Tests\Feature\Social;

use App\Models\Agency;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SocialAccountTest extends TestCase
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
    public function it_lists_social_accounts(): void
    {
        SocialAccount::factory()->count(3)->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->get(route('social.accounts.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_creates_social_account(): void
    {
        $response = $this->actingAs($this->user)->post(route('social.accounts.store'), [
            'platform' => 'facebook',
            'account_name' => 'Test Account',
            'access_token' => 'test_token',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('social_accounts', ['account_name' => 'Test Account']);
    }

    /** @test */
    public function it_validates_social_account_creation(): void
    {
        $response = $this->actingAs($this->user)->post(route('social.accounts.store'), []);
        $response->assertSessionHasErrors(['platform', 'account_name']);
    }

    /** @test */
    public function it_shows_social_account(): void
    {
        $account = SocialAccount::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->get(route('social.accounts.show', $account));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_updates_social_account(): void
    {
        $account = SocialAccount::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->put(route('social.accounts.update', $account), [
            'account_name' => 'Updated Account',
        ]);
        $response->assertRedirect();
    }

    /** @test */
    public function it_deletes_social_account(): void
    {
        $account = SocialAccount::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->delete(route('social.accounts.destroy', $account));
        $response->assertRedirect();
        $this->assertSoftDeleted('social_accounts', ['id' => $account->id]);
    }

    /** @test */
    public function it_prevents_unauthorized_access(): void
    {
        $otherAgency = Agency::factory()->create();
        $account = SocialAccount::factory()->create(['agency_id' => $otherAgency->id]);
        $response = $this->actingAs($this->user)->get(route('social.accounts.show', $account));
        $response->assertForbidden();
    }
}
