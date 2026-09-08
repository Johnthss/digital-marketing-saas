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
    public function it_creates_a_workflow(): void
    {
        $response = $this->actingAs($this->user)->post(route('workflows.store'), [
            'name' => 'Test Workflow',
            'trigger_type' => 'manual',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('workflows', ['name' => 'Test Workflow']);
    }

    /** @test */
    public function it_validates_workflow_creation(): void
    {
        $response = $this->actingAs($this->user)->post(route('workflows.store'), []);
        $response->assertSessionHasErrors(['name', 'trigger_type']);
    }

    /** @test */
    public function it_shows_a_workflow(): void
    {
        $workflow = Workflow::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->get(route('workflows.show', $workflow));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_deletes_a_workflow(): void
    {
        $workflow = Workflow::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->delete(route('workflows.destroy', $workflow));
        $response->assertRedirect();
        $this->assertSoftDeleted('workflows', ['id' => $workflow->id]);
    }

    /** @test */
    public function it_prevents_access_to_other_agency_workflows(): void
    {
        $otherAgency = Agency::factory()->create();
        $workflow = Workflow::factory()->create(['agency_id' => $otherAgency->id]);
        $response = $this->actingAs($this->user)->get(route('workflows.show', $workflow));
        $response->assertForbidden();
    }

    /** @test */
    public function it_requires_auth(): void
    {
        $response = $this->get(route('workflows.index'));
        $response->assertRedirect(route('login'));
    }
}
