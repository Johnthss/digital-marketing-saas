<?php

namespace Tests\Feature\Api;

use App\Models\Agency;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiInvoiceTest extends TestCase
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
    public function it_lists_invoices(): void
    {
        Invoice::factory()->count(3)->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->getJson('/api/v1/invoices');
        $response->assertOk();
        $response->assertJsonCount(3, 'data');
    }

    /** @test */
    public function it_creates_an_invoice(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/v1/invoices', [
            'total' => 100.00,
        ]);
        $response->assertCreated();
        $this->assertDatabaseHas('invoices', ['total' => 100.00]);
    }

    /** @test */
    public function it_validates_invoice_creation(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/v1/invoices', []);
        $response->assertUnprocessable();
    }

    /** @test */
    public function it_shows_an_invoice(): void
    {
        $invoice = Invoice::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->getJson("/api/v1/invoices/{$invoice->id}");
        $response->assertOk();
        $response->assertJsonPath('data.id', $invoice->id);
    }

    /** @test */
    public function it_prevents_access_to_other_agency_invoices(): void
    {
        $otherAgency = Agency::factory()->create();
        $invoice = Invoice::factory()->create(['agency_id' => $otherAgency->id]);
        $response = $this->actingAs($this->user)->getJson("/api/v1/invoices/{$invoice->id}");
        $response->assertNotFound();
    }
}
