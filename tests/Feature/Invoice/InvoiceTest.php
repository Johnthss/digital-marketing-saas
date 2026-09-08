<?php

namespace Tests\Feature\Invoice;

use App\Models\Agency;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTest extends TestCase
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
        $response = $this->actingAs($this->user)->get(route('invoices.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_creates_an_invoice(): void
    {
        $response = $this->actingAs($this->user)->post(route('invoices.store'), [
            'total' => 100.00,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('invoices', ['total' => 100.00]);
    }

    /** @test */
    public function it_validates_invoice_creation(): void
    {
        $response = $this->actingAs($this->user)->post(route('invoices.store'), []);
        $response->assertSessionHasErrors(['total']);
    }

    /** @test */
    public function it_shows_an_invoice(): void
    {
        $invoice = Invoice::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->get(route('invoices.show', $invoice));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_marks_invoice_paid(): void
    {
        $invoice = Invoice::factory()->create(['agency_id' => $this->agency->id, 'status' => 'pending']);
        $response = $this->actingAs($this->user)->post(route('invoices.paid', $invoice));
        $response->assertRedirect();
        $this->assertDatabaseHas('invoices', ['id' => $invoice->id, 'status' => 'paid']);
    }

    /** @test */
    public function it_prevents_access_to_other_agency_invoices(): void
    {
        $otherAgency = Agency::factory()->create();
        $invoice = Invoice::factory()->create(['agency_id' => $otherAgency->id]);
        $response = $this->actingAs($this->user)->get(route('invoices.show', $invoice));
        $response->assertForbidden();
    }

    /** @test */
    public function it_requires_auth(): void
    {
        $response = $this->get(route('invoices.index'));
        $response->assertRedirect(route('login'));
    }
}
