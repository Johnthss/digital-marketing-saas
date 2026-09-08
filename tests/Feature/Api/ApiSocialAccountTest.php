<?php

namespace Tests\Feature\Api;

use App\Models\Agency;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiSocialAccountTest extends TestCase
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
    public function test_it_lists_accounts(): void
    {
        SocialAccount::factory()->count(3)->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->getJson('/api/v1/accounts');
        $response->assertOk();
        $response->assertJsonCount(3, 'data');
    }

    /** @test */
    public function test_it_creates_an_account(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/v1/accounts', [
            'platform' => 'twitter',
            'platform_display_name' => 'Test Account',
            'access_token' => 'test_token',
        ]);
        $response->assertCreated();
        $this->assertDatabaseHas('social_accounts', ['platform_display_name' => 'Test Account']);
    }

    /** @test */
    public function test_it_validates_account_creation(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/v1/accounts', []);
        $response->assertUnprocessable();
    }

    /** @test */
    public function test_it_shows_an_account(): void
    {
        $account = SocialAccount::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->getJson("/api/v1/accounts/{$account->id}");
        $response->assertOk();
        $response->assertJsonPath('id', $account->id);
    }

    /** @test */
    public function test_it_deletes_an_account(): void
    {
        $account = SocialAccount::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->deleteJson("/api/v1/accounts/{$account->id}");
        $response->assertNoContent();
        $this->assertSoftDeleted('social_accounts', ['id' => $account->id]);
    }

    /** @test */
    public function test_it_prevents_access_to_other_agency_accounts(): void
    {
        $otherAgency = Agency::factory()->create();
        $account = SocialAccount::factory()->create(['agency_id' => $otherAgency->id]);
        $response = $this->actingAs($this->user)->getJson("/api/v1/accounts/{$account->id}");
        $response->assertNotFound();
    }
}
