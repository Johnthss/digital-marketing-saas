<?php

namespace Tests\Feature\Workflow;

use App\Models\Agency;
use App\Models\User;
use App\Models\Workflow;
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
        $this->user = User::factory()->create(['agency_id' => $this->agency->id]);
    }

    /** @test */
    public function it_lists_workflows(): void
    {
        Workflow::factory()->count(3)->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->get(route('workflows.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_creates_workflow(): void
    {
        $response = $this->actingAs($this->user)->post(route('workflows.store'), [
            'name' => 'Test Workflow',
            'trigger_type' => 'comment_received',
            'actions' => [['type' => 'send_notification', 'config' => ['message' => 'Test']]],
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('workflows', ['name' => 'Test Workflow']);
    }

    /** @test */
    public function it_validates_workflow_creation(): void
    {
        $response = $this->actingAs($this->user)->post(route('workflows.store'), []);
        $response->assertSessionHasErrors(['name', 'trigger_type', 'actions']);
    }

    /** @test */
    public function it_shows_workflow(): void
    {
        $workflow = Workflow::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->get(route('workflows.show', $workflow));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_updates_workflow(): void
    {
        $workflow = Workflow::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->put(route('workflows.update', $workflow), [
            'name' => 'Updated Workflow',
        ]);
        $response->assertRedirect();
    }

    /** @test */
    public function it_deletes_workflow(): void
    {
        $workflow = Workflow::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->delete(route('workflows.destroy', $workflow));
        $response->assertRedirect();
        $this->assertSoftDeleted('workflows', ['id' => $workflow->id]);
    }

    /** @test */
    public function it_toggles_workflow_status(): void
    {
        $workflow = Workflow::factory()->create(['agency_id' => $this->agency->id, 'status' => 'draft']);
        $response = $this->actingAs($this->user)->post(route('workflows.toggle', $workflow));
        $response->assertRedirect();
    }

    /** @test */
    public function it_shows_workflow_versions(): void
    {
        $workflow = Workflow::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->get(route('workflows.versions', $workflow));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_restores_workflow_version(): void
    {
        $workflow = Workflow::factory()->create(['agency_id' => $this->agency->id]);
        $version = $workflow->createVersion('Test version');
        $response = $this->actingAs($this->user)->post(route('workflows.versions.restore', [$workflow, $version]));
        $response->assertRedirect();
    }

    /** @test */
    public function it_shows_webhook_info(): void
    {
        $workflow = Workflow::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->get(route('workflows.webhook', $workflow));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_regenerates_webhook_secret(): void
    {
        $workflow = Workflow::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->post(route('workflows.webhook.regenerate', $workflow));
        $response->assertRedirect();
    }

    /** @test */
    public function it_shows_visual_builder(): void
    {
        $response = $this->actingAs($this->user)->get(route('workflows.builder'));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_tests_workflow(): void
    {
        $workflow = Workflow::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->post(route('workflows.execute', $workflow), [
            'trigger_data' => [],
        ]);
        $response->assertJson(['success' => true]);
    }

    /** @test */
    public function it_prevents_unauthorized_access(): void
    {
        $otherAgency = Agency::factory()->create();
        $workflow = Workflow::factory()->create(['agency_id' => $otherAgency->id]);
        $response = $this->actingAs($this->user)->get(route('workflows.show', $workflow));
        $response->assertForbidden();
    }
}
