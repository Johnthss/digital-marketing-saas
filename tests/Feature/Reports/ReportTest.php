<?php

namespace Tests\Feature\Report;

use App\Models\Agency;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
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
    public function it_lists_reports(): void
    {
        Report::factory()->count(3)->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->get(route('reports.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_creates_a_report(): void
    {
        $response = $this->actingAs($this->user)->post(route('reports.store'), [
            'name' => 'Test Report',
            'type' => 'analytics',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('reports', ['name' => 'Test Report']);
    }

    /** @test */
    public function it_validates_report_creation(): void
    {
        $response = $this->actingAs($this->user)->post(route('reports.store'), []);
        $response->assertSessionHasErrors(['name', 'type']);
    }

    /** @test */
    public function it_shows_a_report(): void
    {
        $report = Report::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->get(route('reports.show', $report));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_deletes_a_report(): void
    {
        $report = Report::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->delete(route('reports.destroy', $report));
        $response->assertRedirect();
        $this->assertSoftDeleted('reports', ['id' => $report->id]);
    }

    /** @test */
    public function it_prevents_access_to_other_agency_reports(): void
    {
        $otherAgency = Agency::factory()->create();
        $report = Report::factory()->create(['agency_id' => $otherAgency->id]);
        $response = $this->actingAs($this->user)->get(route('reports.show', $report));
        $response->assertForbidden();
    }

    /** @test */
    public function it_requires_auth(): void
    {
        $response = $this->get(route('reports.index'));
        $response->assertRedirect(route('login'));
    }
}
