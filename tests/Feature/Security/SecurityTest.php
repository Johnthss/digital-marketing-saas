<?php

namespace Tests\Feature\Security;

use App\Models\Agency;
use App\Models\Campaign;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\SocialAccount;
use App\Models\SocialPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_cross_agency_post_access_denied(): void
    {
        $agency1 = Agency::factory()->create(['subscription_plan' => 'starter']);
        $agency2 = Agency::factory()->create(['subscription_plan' => 'starter']);
        $user1 = User::factory()->create(['agency_id' => $agency1->id, 'role' => 'owner']);
        $post = SocialPost::factory()->create(['agency_id' => $agency2->id]);

        $response = $this->actingAs($user1)->get("/social/posts/{$post->id}");
        $response->assertStatus(403);
    }

    public function test_cross_agency_campaign_access_denied(): void
    {
        $agency1 = Agency::factory()->create(['subscription_plan' => 'starter']);
        $agency2 = Agency::factory()->create(['subscription_plan' => 'starter']);
        $user1 = User::factory()->create(['agency_id' => $agency1->id, 'role' => 'owner']);
        $campaign = Campaign::factory()->create(['agency_id' => $agency2->id]);

        $response = $this->actingAs($user1)->get("/campaigns/{$campaign->id}");
        $response->assertStatus(403);
    }

    public function test_cross_agency_invoice_access_denied(): void
    {
        $agency1 = Agency::factory()->create(['subscription_plan' => 'starter']);
        $agency2 = Agency::factory()->create(['subscription_plan' => 'starter']);
        $user1 = User::factory()->create(['agency_id' => $agency1->id, 'role' => 'owner']);
        $invoice = Invoice::factory()->create(['agency_id' => $agency2->id]);

        $response = $this->actingAs($user1)->get("/invoices/{$invoice->id}");
        $response->assertStatus(403);
    }

    public function test_inactive_agency_cannot_access(): void
    {
        $agency = Agency::factory()->create(['subscription_plan' => 'starter', 'status' => 'cancelled']);
        $user = User::factory()->create(['agency_id' => $agency->id, 'role' => 'owner']);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(403);
    }

    public function test_no_agency_user_cannot_access(): void
    {
        $user = User::factory()->create(['agency_id' => null, 'role' => 'owner']);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(403);
    }

    public function test_member_cannot_invite_team(): void
    {
        $agency = Agency::factory()->create(['subscription_plan' => 'starter']);
        $member = User::factory()->create(['agency_id' => $agency->id, 'role' => 'member']);

        $response = $this->actingAs($member)->post('/agency/team/invite', [
            'name' => 'New Member',
            'email' => 'new@test.com',
            'role' => 'manager',
        ]);
        $this->assertTrue(in_array($response->getStatusCode(), [302, 403]));
    }

    public function test_manager_cannot_remove_members(): void
    {
        $agency = Agency::factory()->create(['subscription_plan' => 'starter']);
        $manager = User::factory()->create(['agency_id' => $agency->id, 'role' => 'manager']);
        $member = User::factory()->create(['agency_id' => $agency->id, 'role' => 'member']);

        $response = $this->actingAs($manager)->delete("/agency/team/{$member->id}");
        $this->assertTrue(in_array($response->getStatusCode(), [302, 403]));
    }

    public function test_social_account_cross_agency_delete_denied(): void
    {
        $agency1 = Agency::factory()->create(['subscription_plan' => 'starter']);
        $agency2 = Agency::factory()->create(['subscription_plan' => 'starter']);
        $user1 = User::factory()->create(['agency_id' => $agency1->id, 'role' => 'owner']);
        $account = SocialAccount::factory()->create(['agency_id' => $agency2->id]);

        $response = $this->actingAs($user1)->delete("/social/accounts/{$account->id}");
        $response->assertStatus(403);
    }

    public function test_client_edit_cross_agency_denied(): void
    {
        $agency1 = Agency::factory()->create(['subscription_plan' => 'starter']);
        $agency2 = Agency::factory()->create(['subscription_plan' => 'starter']);
        $user1 = User::factory()->create(['agency_id' => $agency1->id, 'role' => 'owner']);
        $client2 = Client::factory()->create(['agency_id' => $agency2->id]);

        $response = $this->actingAs($user1)->get("/clients/{$client2->id}/edit");
        $response->assertStatus(403);
    }

    public function test_campaign_delete_cross_agency_denied(): void
    {
        $agency1 = Agency::factory()->create(['subscription_plan' => 'starter']);
        $agency2 = Agency::factory()->create(['subscription_plan' => 'starter']);
        $user1 = User::factory()->create(['agency_id' => $agency1->id, 'role' => 'owner']);
        $campaign2 = Campaign::factory()->create(['agency_id' => $agency2->id]);

        $response = $this->actingAs($user1)->delete("/campaigns/{$campaign2->id}");
        $response->assertStatus(403);
    }

    public function test_invoice_delete_cross_agency_denied(): void
    {
        $agency1 = Agency::factory()->create(['subscription_plan' => 'starter']);
        $agency2 = Agency::factory()->create(['subscription_plan' => 'starter']);
        $user1 = User::factory()->create(['agency_id' => $agency1->id, 'role' => 'owner']);
        $invoice2 = Invoice::factory()->create(['agency_id' => $agency2->id]);

        $response = $this->actingAs($user1)->delete("/invoices/{$invoice2->id}");
        $response->assertStatus(403);
    }
}
