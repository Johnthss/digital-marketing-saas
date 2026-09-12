<?php

namespace Tests\Feature\Invoice;

use App\Models\Agency;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceFeatureTest extends TestCase
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_lists_invoices(): void
    {
        Invoice::factory()->count(3)->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->get(route('invoices.index'));
        $response->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_creates_invoice(): void
    {
        $response = $this->actingAs($this->user)->post(route('invoices.store'), [
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addMonth()->toDateString(),
            'items' => [['description' => 'Service', 'quantity' => 1, 'unit_price' => 100]],
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('invoices', ['agency_id' => $this->agency->id, 'total' => 100]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_shows_invoice(): void
    {
        $invoice = Invoice::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->get(route('invoices.show', $invoice));
        $response->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_updates_invoice(): void
    {
        $invoice = Invoice::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->put(route('invoices.update', $invoice), [
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addMonth()->toDateString(),
            'notes' => 'Updated invoice',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('invoices', ['id' => $invoice->id, 'notes' => 'Updated invoice']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_deletes_invoice(): void
    {
        $invoice = Invoice::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->delete(route('invoices.destroy', $invoice));
        $response->assertRedirect();
        $this->assertDatabaseMissing('invoices', ['id' => $invoice->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_marks_invoice_paid(): void
    {
        $invoice = Invoice::factory()->create(['agency_id' => $this->agency->id, 'status' => 'pending']);
        $response = $this->actingAs($this->user)->post(route('invoices.paid', $invoice), [
            'payment_method' => 'manual',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('invoices', ['id' => $invoice->id, 'status' => 'paid']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_prevents_unauthorized_access(): void
    {
        $otherAgency = Agency::factory()->create();
        $invoice = Invoice::factory()->create(['agency_id' => $otherAgency->id]);
        $response = $this->actingAs($this->user)->get(route('invoices.show', $invoice));
        $response->assertForbidden();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_filters_by_status(): void
    {
        Invoice::factory()->create(['agency_id' => $this->agency->id, 'status' => 'pending']);
        Invoice::factory()->create(['agency_id' => $this->agency->id, 'status' => 'paid']);
        $response = $this->actingAs($this->user)->get(route('invoices.index', ['status' => 'pending']));
        $response->assertStatus(200);
    }
}
