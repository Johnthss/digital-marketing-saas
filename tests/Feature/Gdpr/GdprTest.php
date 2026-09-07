<?php

namespace Tests\Feature\Gdpr;

use App\Models\Agency;
use App\Models\ConsentRecord;
use App\Models\DataDeletionRequest;
use App\Models\DataExportRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GdprTest extends TestCase
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
    public function it_shows_privacy_dashboard(): void
    {
        $response = $this->actingAs($this->user)->get(route('gdpr.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_requests_data_export(): void
    {
        $response = $this->actingAs($this->user)->post(route('gdpr.export'), [
            'export_types' => ['posts', 'campaigns', 'clients'],
        ]);
        $response->assertRedirect(route('gdpr.index'));
        $this->assertDatabaseHas('data_export_requests', [
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function it_requests_account_deletion(): void
    {
        $response = $this->actingAs($this->user)->post(route('gdpr.delete'), [
            'reason' => 'No longer needed',
        ]);
        $response->assertRedirect(route('gdpr.index'));
        $this->assertDatabaseHas('data_deletion_requests', [
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function it_updates_consent(): void
    {
        $response = $this->actingAs($this->user)->post(route('gdpr.consent'), [
            'consent_type' => 'marketing',
            'granted' => true,
        ]);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('consent_records', [
            'user_id' => $this->user->id,
            'granted' => true,
        ]);
    }

    /** @test */
    public function it_validates_export_types(): void
    {
        $response = $this->actingAs($this->user)->post(route('gdpr.export'), [
            'export_types' => [],
        ]);
        $response->assertSessionHasErrors('export_types');
    }

    /** @test */
    public function it_validates_consent_type(): void
    {
        $response = $this->actingAs($this->user)->post(route('gdpr.consent'), [
            'consent_type' => 'invalid_type',
            'granted' => true,
        ]);
        $response->assertSessionHasErrors('consent_type');
    }

    /** @test */
    public function it_tracks_consent_history(): void
    {
        ConsentRecord::create([
            'user_id' => $this->user->id,
            'consent_type' => 'marketing',
            'granted' => true,
        ]);
        ConsentRecord::create([
            'user_id' => $this->user->id,
            'consent_type' => 'marketing',
            'granted' => false,
        ]);
        $this->assertDatabaseCount('consent_records', 2);
        $latest = ConsentRecord::where('user_id', $this->user->id)
            ->where('consent_type', 'marketing')
            ->orderBy('created_at', 'desc')
            ->first();
        $this->assertFalse($latest->granted);
    }

    /** @test */
    public function it_shows_export_requests(): void
    {
        DataExportRequest::factory()->count(3)->create(['user_id' => $this->user->id]);
        $response = $this->actingAs($this->user)->get(route('gdpr.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_shows_deletion_requests(): void
    {
        DataDeletionRequest::factory()->create(['user_id' => $this->user->id]);
        $response = $this->actingAs($this->user)->get(route('gdpr.index'));
        $response->assertStatus(200);
    }
}
