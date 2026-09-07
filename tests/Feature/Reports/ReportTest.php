<?php

namespace Tests\Feature\Reports;

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
    public function it_creates_report(): void
    {
        $response = $this->actingAs($this->user)->post(route('reports.store'), [
            'name' => 'Social Report',
            'type' => 'social',
            'format' => 'pdf',
            'schedule' => 'once',
        ]);
        $response->assertRedirect(route('reports.index'));
        $this->assertDatabaseHas('reports', ['name' => 'Social Report']);
    }

    /** @test */
    public function it_validates_report_creation(): void
    {
        $response = $this->actingAs($this->user)->post(route('reports.store'), []);
        $response->assertSessionHasErrors(['name', 'type', 'format', 'schedule']);
    }

    /** @test */
    public function it_shows_report(): void
    {
        $report = Report::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->get(route('reports.show', $report));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_deletes_report(): void
    {
        $report = Report::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->delete(route('reports.destroy', $report));
        $response->assertRedirect(route('reports.index'));
        $this->assertSoftDeleted('reports', ['id' => $report->id]);
    }

    /** @test */
    public function it_prevents_unauthorized_access(): void
    {
        $otherAgency = Agency::factory()->create();
        $report = Report::factory()->create(['agency_id' => $otherAgency->id]);
        $response = $this->actingAs($this->user)->get(route('reports.show', $report));
        $response->assertForbidden();
    }

    /** @test */
    public function it_generates_report(): void
    {
        $report = Report::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->post(route('reports.generate', $report));
        $response->assertRedirect();
    }

    /** @test */
    public function it_filters_by_type(): void
    {
        Report::factory()->create(['agency_id' => $this->agency->id, 'type' => 'social']);
        Report::factory()->create(['agency_id' => $this->agency->id, 'type' => 'email']);
        $response = $this->actingAs($this->user)->get(route('reports.index', ['type' => 'social']));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_downloads_report(): void
    {
        $report = Report::factory()->create([
            'agency_id' => $this->agency->id,
            'file_path' => 'reports/test.pdf',
        ]);
        $response = $this->actingAs($this->user)->get(route('reports.download', $report));
        $response->assertStatus(200);
    }
}
