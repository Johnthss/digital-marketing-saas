<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\Billing\StripeGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BillingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'agency']);
    }

    public function index(Request $request)
    {
        $agency = $request->user()->agency;
        $invoices = Invoice::where('agency_id', $agency->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $plans = config('stripe.plans');
        $currentPlan = $agency->subscription_plan ?? 'free';

        return view('billing.index', compact('agency', 'invoices', 'plans', 'currentPlan'));
    }

    public function upgrade(Request $request)
    {
        $agency = $request->user()->agency;
        $plans = config('stripe.plans');
        $currentPlan = $agency->subscription_plan ?? 'free';

        return view('billing.upgrade', compact('agency', 'plans', 'currentPlan'));
    }

    public function checkout(Request $request, string $plan)
    {
        $agency = $request->user()->agency;

        if ($plan === 'free') {
            return back()->with('error', 'Free plan does not require checkout.');
        }

        if (! config("stripe.plans.{$plan}.monthly")) {
            return back()->with('error', 'Invalid plan selected.');
        }

        try {
            $gateway = new StripeGateway;
            $session = $gateway->createCheckoutSession($agency, $plan);

            return redirect($session->url);
        } catch (\Exception $e) {
            Log::error('Checkout failed: '.$e->getMessage());

            return back()->with('error', 'Could not create checkout session. Please try again.');
        }
    }

    public function success(Request $request)
    {
        $agency = $request->user()->agency;

        return view('billing.success', compact('agency'));
    }

    public function cancel(Request $request)
    {
        return redirect()->route('agency.billing')
            ->with('warning', 'Checkout was canceled.');
    }

    public function invoices(Request $request)
    {
        $agency = $request->user()->agency;
        $invoices = Invoice::where('agency_id', $agency->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('billing.invoices', compact('agency', 'invoices'));
    }

    public function downloadInvoice(Request $request, Invoice $invoice)
    {
        $agency = $request->user()->agency;
        if ($invoice->agency_id !== $agency->id) {
            abort(403);
        }

        return redirect()->route('agency.invoices')
            ->with('info', 'Invoice download coming soon.');
    }

    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $gateway = new StripeGateway;
            $gateway->handleWebhook($payload, $sigHeader);

            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            Log::error('Webhook error: '.$e->getMessage());

            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function cancelSubscription(Request $request)
    {
        $agency = $request->user()->agency;

        try {
            $gateway = new StripeGateway;
            $gateway->cancelSubscription($agency);

            return back()->with('success', 'Subscription canceled.');
        } catch (\Exception $e) {
            return back()->with('error', 'Could not cancel subscription.');
        }
    }
}
