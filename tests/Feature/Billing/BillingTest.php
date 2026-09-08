<?php

namespace Tests\Feature\Billing;

use App\Models\Agency;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingTest extends TestCase
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

    public function test_it_shows_billing_page(): void
    {
        Invoice::factory()->count(3)->create(['agency_id' => $this->agency->id]);
        
        $response = $this->actingAs($this->user)->get(route('agency.billing'));
        
        $response->assertOk();
        $response->assertViewIs('agency.billing');
        $response->assertViewHas('invoices');
        $response->assertViewHas('plans');
    }

    public function test_it_shows_invoices(): void
    {
        Invoice::factory()->count(2)->create(['agency_id' => $this->agency->id]);
        
        $response = $this->actingAs($this->user)->get(route('agency.invoices'));
        
        $response->assertOk();
        $response->assertViewIs('agency.invoices');
        $response->assertViewHas('invoices');
    }

    public function test_it_redirects_checkout_for_free_plan(): void
    {
        $response = $this->actingAs($this->user)->get(route('billing.checkout', 'free'));
        
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_it_shows_success_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('billing.success'));
        
        $response->assertOk();
        $response->assertViewIs('agency.billing-success');
    }

    public function test_it_handles_cancel(): void
    {
        $response = $this->actingAs($this->user)->get(route('billing.cancel'));
        
        $response->assertRedirect(route('agency.billing'));
        $response->assertSessionHas('warning');
    }

    public function test_it_requires_auth(): void
    {
        $response = $this->get(route('agency.billing'));
        
        $response->assertRedirect(route('login'));
    }

    public function test_it_prevents_invoice_access_from_other_agencies(): void
    {
        $otherAgency = Agency::factory()->create();
        $invoice = Invoice::factory()->create(['agency_id' => $otherAgency->id]);
        
        $response = $this->actingAs($this->user)->get(route('billing.invoice.download', $invoice));
        
        $response->assertForbidden();
    }
}