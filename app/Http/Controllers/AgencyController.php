<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AgencyController extends Controller
{
    public function settings(Request $request)
    {
        $user = $request->user();
        $agency = $user->agency;

        return view('agency.settings', compact('user', 'agency'));
    }

    public function updateSettings(Request $request)
    {
        $user = $request->user();
        $agency = $user->agency;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:agencies,email,' . $agency->id,
            'timezone' => 'nullable|string|max:50',
            'currency' => 'nullable|string|max:3',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'website' => 'nullable|url',
            'description' => 'nullable|string',
        ]);

        $agency->update($validated);

        return redirect()->route('agency.settings')->with('success', 'Agency settings updated.');
    }

    public function team(Request $request)
    {
        $user = $request->user();
        $agency = $user->agency;

        $members = User::where('agency_id', $agency->id)
            ->orderBy('role')
            ->orderBy('name')
            ->paginate(20);

        return view('agency.team', compact('user', 'agency', 'members'));
    }

    public function inviteMember(Request $request)
    {
        $user = $request->user();
        $agency = $user->agency;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,manager,member',
        ]);

        $member = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($password = str()->random(16)),
            'agency_id' => $agency->id,
            'role' => $validated['role'],
            'is_active' => true,
            'is_approved' => true,
        ]);

        $agency->increment('users_count');

        $member->notify(new \App\Notifications\TeamInvitationNotification($agency, $password));

        return back()->with('success', 'Team member invited successfully.');
    }

    public function updateMemberRole(Request $request, User $member)
    {
        $user = $request->user();
        $agency = $user->agency;

        if ($member->agency_id !== $agency->id) {
            abort(403);
        }

        $validated = $request->validate([
            'role' => 'required|in:admin,manager,member',
        ]);

        $member->update(['role' => $validated['role']]);

        return back()->with('success', 'Member role updated.');
    }

    public function removeMember(Request $request, User $member)
    {
        $user = $request->user();
        $agency = $user->agency;

        if ($member->agency_id !== $agency->id) {
            abort(403);
        }

        if ($member->id === $user->id) {
            return back()->with('error', 'You cannot remove yourself.');
        }

        if ($member->isOwner()) {
            return back()->with('error', 'Cannot remove the agency owner.');
        }

        $member->delete();
        $agency->decrement('users_count');

        return back()->with('success', 'Member removed.');
    }

    public function billing(Request $request)
    {
        $user = $request->user();
        $agency = $user->agency;

        $invoices = \App\Models\Invoice::where('agency_id', $agency->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        $plans = config('stripe.plans');
        $currentPlan = $agency->subscription_plan ?? 'free';

        return view('agency.billing', compact('agency', 'invoices', 'plans', 'currentPlan', 'user'));
    }

    public function upgrade(Request $request)
    {
        $user = $request->user();
        $agency = $user->agency;

        $validated = $request->validate([
            'plan' => 'required|in:starter,pro,enterprise',
        ]);

        $plan = $validated['plan'];

        // In production: redirect to Stripe Checkout
        $agency->update([
            'subscription_plan' => $plan,
            'subscription_start' => now(),
            'subscription_status' => 'active',
        ]);

        return redirect()->route('agency.billing')
            ->with('success', "Successfully upgraded to {$plan} plan!");
    }
}
