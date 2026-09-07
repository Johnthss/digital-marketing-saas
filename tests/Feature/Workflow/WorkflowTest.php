<?php

namespace Tests\Feature\Workflow;

use App\Models\Agency;
use App\Models\User;
use App\Models\Workflow;
use App\Models\WorkflowTemplate;
use App\Models\WorkflowVersion;
use App\Models\WorkflowWebhookLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkflowTest extends TestCase
{
    use RefreshDatabase;

    private Agency $agency;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->agency = Agency::factory()->create();
        $this->user = User::factory()->create([
            'agency_id' => $this->agency->id,
            'password' => bcrypt('password'),
        ]);
    }

    public function test_workflow_index_returns_200(): void
    {
        $response = $this->actingAs($this->user)->get('/workflows');
        $response->assertStatus(200);
    }

    public function test_workflow_creation_creates_initial_version(): void
    {
        $response = $this->actingAs($this->user)->post('/workflows', [
            'name' => 'Test Workflow',
            'trigger_type' => 'post_published',
            'actions' => [['type' => 'send_notification', 'config' => ['message' => 'Test']]],
        ]);

        $workflow = Workflow::first();
        $this->assertNotNull($workflow);
        $this->assertEquals(1, $workflow->versions()->count());
        $this->assertEquals('Initial version', $workflow->versions->first()->change_notes);
    }

    public function test_workflow_update_creates_new_version(): void
    {
        $workflow = Workflow::factory()->create(['agency_id' => $this->agency->id]);

        $this->actingAs($this->user)->put("/workflows/{$workflow->id}", [
            'name' => 'Updated Name',
            'trigger_type' => 'comment_received',
            'actions' => [['type' => 'auto_reply', 'config' => []]],
            'change_notes' => 'Changed trigger',
        ]);

        $workflow->refresh();
        $this->assertEquals(1, $workflow->versions()->count());
        $this->assertEquals('Changed trigger', $workflow->versions()->latest()->first()->change_notes);
    }

    public function test_workflow_restore_version(): void
    {
        $workflow = Workflow::factory()->create([
            'agency_id' => $this->agency->id,
            'name' => 'Original',
            'trigger_type' => 'post_published',
        ]);

        $version = $workflow->createVersion('Original version', $this->user->id);

        // Update workflow
        $workflow->update(['name' => 'Changed', 'trigger_type' => 'comment_received']);
        $this->assertEquals('Changed', $workflow->name);

        // Restore to version 1
        $this->actingAs($this->user)->post("/workflows/{$workflow->id}/versions/{$version->id}/restore");
        
        $workflow->refresh();
        $this->assertEquals('Original', $workflow->name);
        $this->assertEquals('post_published', $workflow->trigger_type);
    }

    public function test_workflow_template_creation(): void
    {
        $template = WorkflowTemplate::create([
            'name' => 'Test Template',
            'slug' => 'test-template',
            'description' => 'A test template',
            'category' => 'social',
            'icon' => 'fa-bell',
            'nodes' => [['id' => 1, 'type' => 'trigger']],
            'connections' => [],
            'is_public' => true,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('workflow_templates', ['slug' => 'test-template']);
        $this->assertEquals(0, $template->usage_count);
    }

    public function test_workflow_from_template(): void
    {
        $template = WorkflowTemplate::create([
            'name' => 'Auto Reply',
            'slug' => 'auto-reply',
            'description' => 'Auto reply template',
            'category' => 'social',
            'icon' => 'fa-reply',
            'nodes' => [
                ['id' => 1, 'type' => 'trigger', 'subtype' => 'comment_received', 'x' => 100, 'y' => 200, 'config' => [], 'label' => 'Comment Received'],
                ['id' => 2, 'type' => 'action', 'subtype' => 'auto_reply', 'x' => 350, 'y' => 200, 'config' => ['message' => 'Thanks!'], 'label' => 'Auto Reply'],
            ],
            'connections' => [['from' => 1, 'to' => 2]],
            'is_public' => true,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->get('/workflows/templates/auto-reply');
        $response->assertStatus(302); // Redirects to builder

        $workflow = Workflow::where('name', 'Auto Reply')->first();
        $this->assertNotNull($workflow);
        $this->assertEquals($this->agency->id, $workflow->agency_id);
    }

    public function test_workhook_creation_and_execution(): void
    {
        $workflow = Workflow::factory()->create([
            'agency_id' => $this->agency->id,
            'status' => 'active',
        ]);
        $workflow->generateWebhookSecret();
        $workflow->refresh();

        $this->assertNotNull($workflow->webhook_secret);
        $this->assertNotNull($workflow->webhook_url);
    }

    public function test_workhook_endpoint_rejects_invalid_secret(): void
    {
        $workflow = Workflow::factory()->create([
            'agency_id' => $this->agency->id,
            'status' => 'active',
        ]);
        $workflow->generateWebhookSecret();
        $workflow->refresh();

        $response = $this->postJson("/api/workflows/{$workflow->id}/webhook/invalid-secret", [
            'event' => 'test',
        ]);

        $response->assertStatus(401);
    }

    public function test_workhook_endpoint_accepts_valid_secret(): void
    {
        $workflow = Workflow::factory()->create([
            'agency_id' => $this->agency->id,
            'status' => 'active',
            'trigger_type' => 'webhook',
            'actions' => [['type' => 'send_notification', 'config' => ['message' => 'Test']]],
        ]);
        $workflow->generateWebhookSecret();
        $workflow->refresh();

        $response = $this->postJson("/api/workflows/{$workflow->id}/webhook/{$workflow->webhook_secret}", [
            'event' => 'new_order',
            'data' => ['order_id' => 123],
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('workflow_webhook_logs', [
            'workflow_id' => $workflow->id,
            'status' => 'processed',
        ]);
    }

    public function test_workhook_logs_are_recorded(): void
    {
        $workflow = Workflow::factory()->create([
            'agency_id' => $this->agency->id,
            'status' => 'active',
            'trigger_type' => 'webhook',
            'actions' => [['type' => 'send_notification', 'config' => []]],
        ]);
        $workflow->generateWebhookSecret();
        $workflow->refresh();

        // Send webhook
        $this->postJson("/api/workflows/{$workflow->id}/webhook/{$workflow->webhook_secret}", [
            'event' => 'test',
        ]);

        $this->assertEquals(1, $workflow->webhookLogs()->count());
        $log = $workflow->webhookLogs()->first();
        $this->assertEquals('test', $log->event_type);
        $this->assertEquals('processed', $log->status);
    }

    public function test_workflow_version_pagination(): void
    {
        $workflow = Workflow::factory()->create(['agency_id' => $this->agency->id]);

        // Create multiple versions
        for ($i = 1; $i <= 5; $i++) {
            $workflow->createVersion("Change {$i}", $this->user->id);
        }

        $response = $this->actingAs($this->user)->get("/workflows/{$workflow->id}/versions");
        $response->assertStatus(200);
    }

    public function test_workflow_webhook_info_page(): void
    {
        $workflow = Workflow::factory()->create(['agency_id' => $this->agency->id]);

        $response = $this->actingAs($this->user)->get("/workflows/{$workflow->id}/webhook");
        $response->assertStatus(200);
    }

    public function test_workflow_webhook_regenerate_secret(): void
    {
        $workflow = Workflow::factory()->create(['agency_id' => $this->agency->id]);
        $workflow->generateWebhookSecret();
        $workflow->refresh();
        $oldSecret = $workflow->webhook_secret;

        $response = $this->actingAs($this->user)->post("/workflows/{$workflow->id}/webhook/regenerate");

        $workflow->refresh();
        $this->assertNotEquals($oldSecret, $workflow->webhook_secret);
    }

    public function test_workflow_loop_action_executes(): void
    {
        $workflow = Workflow::factory()->create([
            'agency_id' => $this->agency->id,
            'trigger_type' => 'schedule',
            'actions' => [
                [
                    'type' => 'loop',
                    'config' => [
                        'iterations' => 3,
                        'actions' => [
                            ['type' => 'send_notification', 'config' => ['message' => 'Loop test']],
                        ],
                    ],
                ],
            ],
        ]);

        $engine = app(\App\Services\Workflow\WorkflowEngine::class);
        $execution = $engine->execute($workflow);

        $this->assertEquals('success', $execution->status);
        $this->assertNotNull($execution->action_results);
    }
}
