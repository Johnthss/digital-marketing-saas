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

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $agency = Agency::factory()->create(['subscription_plan' => 'starter']);
        $this->user = User::factory()->create(['agency_id' => $agency->id, 'role' => 'owner']);
    }

    public function test_invoices_index_requires_authentication(): void
    {
        $response = $this->get('/invoices');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_invoices(): void
    {
        $response = $this->actingAs($this->user)->get('/invoices');
        $response->assertStatus(200);
    }

    public function test_user_can_create_invoice(): void
    {
        $response = $this->actingAs($this->user)->post('/invoices', [
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'items' => [
                ['description' => 'Service', 'quantity' => 1, 'unit_price' => 100],
            ],
        ]);

        $response->assertRedirect('/invoices/1');
        $this->assertDatabaseHas('invoices', ['total' => 100]);
    }

    public function test_user_can_view_invoice(): void
    {
        $invoice = Invoice::factory()->create(['agency_id' => $this->user->agency_id]);
        $response = $this->actingAs($this->user)->get("/invoices/{$invoice->id}");
        $response->assertStatus(200);
    }

    public function test_user_cannot_view_other_agency_invoice(): void
    {
        $invoice = Invoice::factory()->create();
        $response = $this->actingAs($this->user)->get("/invoices/{$invoice->id}");
        $response->assertStatus(403);
    }

    public function test_user_can_mark_invoice_paid(): void
    {
        $invoice = Invoice::factory()->create([
            'agency_id' => $this->user->agency_id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)->post("/invoices/{$invoice->id}/paid", [
            'payment_method' => 'manual',
            'transaction_id' => 'txn_123456',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('invoices', ['id' => $invoice->id, 'status' => 'paid']);
    }
}
