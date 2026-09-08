<?php

namespace Tests\Feature\Api;

use App\Models\Agency;
use App\Models\User;
use App\Models\Workflow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiWorkflowTest extends TestCase
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
    public function test_it_lists_workflows(): void
    {
        Workflow::factory()->count(3)->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->getJson('/api/v1/workflows');
        $response->assertOk();
        $response->assertJsonCount(3, 'data');
    }

    /** @test */
    public function test_it_creates_a_workflow(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/v1/workflows', [
            'name' => 'Test Workflow',
            'trigger_type' => 'manual',
            'actions' => [],
        ]);
        $response->assertCreated();
        $this->assertDatabaseHas('workflows', ['name' => 'Test Workflow']);
    }

    /** @test */
    public function test_it_validates_workflow_creation(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/v1/workflows', []);
        $response->assertUnprocessable();
    }

    /** @test */
    public function test_it_shows_a_workflow(): void
    {
        $workflow = Workflow::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->getJson("/api/v1/workflows/{$workflow->id}");
        $response->assertOk();
        $response->assertJsonPath('id', $workflow->id);
    }

    /** @test */
    public function test_it_prevents_access_to_other_agency_workflows(): void
    {
        $otherAgency = Agency::factory()->create();
        $workflow = Workflow::factory()->create(['agency_id' => $otherAgency->id]);
        $response = $this->actingAs($this->user)->getJson("/api/v1/workflows/{$workflow->id}");
        $response->assertNotFound();
    }
}
