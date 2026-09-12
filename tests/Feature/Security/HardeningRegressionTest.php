<?php

namespace Tests\Feature\Security;

use App\Models\Agency;
use App\Models\SocialAccount;
use App\Models\SocialPost;
use App\Models\User;
use App\Models\Workflow;
use App\Services\AI\AiContentService;
use App\Services\RBAC\EnterpriseRBACService;
use App\Services\Social\SocialPostService;
use App\Services\Workflow\WorkflowEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class HardeningRegressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_cannot_manage_roles(): void
    {
        $agency = Agency::factory()->create();
        $member = User::factory()->create(['agency_id' => $agency->id, 'role' => 'member']);

        $this->actingAs($member)->get(route('roles.index'))->assertForbidden();
    }

    public function test_actor_cannot_assign_a_role_across_tenants(): void
    {
        $agency = Agency::factory()->create();
        $otherAgency = Agency::factory()->create();
        $target = User::factory()->create(['agency_id' => $otherAgency->id]);
        $role = Role::create(['name' => 'Other editor', 'guard_name' => 'web', 'agency_id' => $otherAgency->id]);

        $this->assertFalse(app(EnterpriseRBACService::class)->assignRoleToUser($target->id, $role->id, $agency->id));
    }

    public function test_social_tokens_are_encrypted_at_rest(): void
    {
        $account = SocialAccount::factory()->create(['access_token' => 'plain-secret']);

        $this->assertSame('plain-secret', $account->fresh()->access_token);
        $this->assertNotSame('plain-secret', DB::table('social_accounts')->where('id', $account->id)->value('access_token'));
    }

    public function test_telegram_webhook_fails_closed_without_configured_secret(): void
    {
        config(['telegram.webhook.secret_token' => '']);

        $this->postJson(route('telegram.webhook'), ['update_id' => 1])->assertUnauthorized();
    }

    public function test_workflow_rejects_private_webhook_targets(): void
    {
        $agency = Agency::factory()->create();
        $workflow = Workflow::factory()->create([
            'agency_id' => $agency->id,
            'conditions' => [],
            'actions' => [['type' => 'webhook', 'config' => ['url' => 'https://127.0.0.1/internal']]],
        ]);
        $engine = new WorkflowEngine(\Mockery::mock(AiContentService::class));

        $this->assertSame('failed', $engine->execute($workflow)->status);
    }

    public function test_platform_failure_never_marks_post_as_published(): void
    {
        Http::fake(['api.twitter.com/*' => Http::response(['detail' => 'denied'], 401)]);
        $account = SocialAccount::factory()->create(['platform' => 'twitter', 'access_token' => 'token', 'is_active' => true]);
        $post = SocialPost::factory()->create([
            'agency_id' => $account->agency_id,
            'social_account_id' => $account->id,
            'platform' => 'twitter',
            'status' => 'draft',
        ]);

        $this->assertFalse(app(SocialPostService::class)->publishPost($post)['success']);
        $this->assertSame('failed', $post->fresh()->status);
        $this->assertNull($post->fresh()->published_at);
    }
}
