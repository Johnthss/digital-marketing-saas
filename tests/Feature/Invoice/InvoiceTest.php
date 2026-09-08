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

    public function test_it_lists_invoices(): void
    {
        Invoice::factory()->count(3)->create(['agency_id' => $this->agency->id]);
        
        $response = $this->actingAs($this->user)->get(route('invoices.index'));
        
        $response->assertOk();
        $response->assertViewIs('invoices.index');
        $response->assertViewHas('invoices');
        $response->assertViewHas('stats');
    }

    public function test_it_creates_an_invoice(): void
    {
        $response = $this->actingAs($this->user)->post(route('invoices.store'), [
            'issue_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'notes' => 'Test invoice',
            'items' => [
                [
                    'description' => 'Service 1',
                    'quantity' => 2,
                    'unit_price' => 100.00,
                ],
                [
                    'description' => 'Service 2',
                    'quantity' => 1,
                    'unit_price' => 50.00,
                ],
            ],
        ]);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('invoices', [
            'agency_id' => $this->agency->id,
            'total' => 250.00,
        ]);
    }

    public function test_it_validates_invoice_creation(): void
    {
        $response = $this->actingAs($this->user)->post(route('invoices.store'), []);
        
        $response->assertSessionHasErrors(['issue_date', 'due_date', 'items']);
    }

    public function test_it_shows_an_invoice(): void
    {
        $invoice = Invoice::factory()->create(['agency_id' => $this->agency->id]);
        
        $response = $this->actingAs($this->user)->get(route('invoices.show', $invoice));
        
        $response->assertOk();
        $response->assertViewIs('invoices.show');
        $response->assertViewHas('invoice');
    }

    public function test_it_prevents_showing_other_agency_invoices(): void
    {
        $otherAgency = Agency::factory()->create();
        $invoice = Invoice::factory()->create(['agency_id' => $otherAgency->id]);
        
        $response = $this->actingAs($this->user)->get(route('invoices.show', $invoice));
        
        $response->assertForbidden();
    }

    public function test_it_edits_an_invoice(): void
    {
        $invoice = Invoice::factory()->create(['agency_id' => $this->agency->id]);
        
        $response = $this->actingAs($this->user)->get(route('invoices.edit', $invoice));
        
        $response->assertOk();
        $response->assertViewIs('invoices.edit');
    }

    public function test_it_updates_an_invoice(): void
    {
        $invoice = Invoice::factory()->create(['agency_id' => $this->agency->id]);
        
        $response = $this->actingAs($this->user)->put(route('invoices.update', $invoice), [
            'issue_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'notes' => 'Updated notes',
        ]);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'notes' => 'Updated notes',
        ]);
    }

    public function test_it_deletes_an_invoice(): void
    {
        $invoice = Invoice::factory()->create(['agency_id' => $this->agency->id]);
        
        $response = $this->actingAs($this->user)->delete(route('invoices.destroy', $invoice));
        
        $response->assertRedirect(route('invoices.index'));
        $this->assertDatabaseMissing('invoices', ['id' => $invoice->id]);
    }

    public function test_it_marks_invoice_as_paid(): void
    {
        $invoice = Invoice::factory()->create(['agency_id' => $this->agency->id]);
        
        $response = $this->actingAs($this->user)->post(route('invoices.paid', $invoice), [
            'payment_method' => 'stripe',
            'transaction_id' => 'txn_123',
        ]);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => 'paid',
        ]);
    }

    public function test_it_requires_auth(): void
    {
        $response = $this->get(route('invoices.index'));
        
        $response->assertRedirect(route('login'));
    }
}