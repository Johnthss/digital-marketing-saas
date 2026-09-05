<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\SocialAccount;
use App\Models\Platform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SocialAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'agency']);
    }

    public function index(Request $request)
    {
        $agency = $request->user()->agency;

        $accounts = SocialAccount::where('agency_id', $agency->id)
            ->orderBy('platform')
            ->get();

        $availablePlatforms = SocialAccount::SUPPORTED_PLATFORMS;

        return view('social.accounts.index', compact('agency', 'accounts', 'availablePlatforms'));
    }

    public function create(Request $request)
    {
        $agency = $request->user()->agency;
        $platforms = SocialAccount::SUPPORTED_PLATFORMS;

        return view('social.accounts.create', compact('agency', 'platforms'));
    }

    public function store(Request $request)
    {
        $agency = $request->user()->agency;

        $validated = $request->validate([
            'platform' => 'required|in:' . implode(',', array_keys(SocialAccount::SUPPORTED_PLATFORMS)),
            'access_token' => 'required|string',
            'refresh_token' => 'nullable|string',
            'platform_account_id' => 'nullable|string',
            'platform_username' => 'nullable|string',
            'platform_display_name' => 'nullable|string',
        ]);

        $account = SocialAccount::create([
            'agency_id' => $agency->id,
            'platform' => $validated['platform'],
            'access_token' => encrypt($validated['access_token']),
            'refresh_token' => isset($validated['refresh_token']) ? encrypt($validated['refresh_token']) : null,
            'platform_account_id' => $validated['platform_account_id'] ?? null,
            'platform_username' => $validated['platform_username'] ?? null,
            'platform_display_name' => $validated['platform_display_name'] ?? null,
            'is_active' => true,
        ]);

        $agency->increment('social_accounts_count');

        return redirect()->route('social.accounts.index')
            ->with('success', 'Social account connected successfully.');
    }

    public function destroy(Request $request, SocialAccount $account)
    {
        $agency = $request->user()->agency;

        if ($account->agency_id !== $agency->id) {
            abort(403);
        }

        $account->delete();
        $agency->decrement('social_accounts_count');

        return redirect()->route('social.accounts.index')
            ->with('success', 'Social account removed.');
    }

    public function toggle(Request $request, SocialAccount $account)
    {
        $agency = $request->user()->agency;

        if ($account->agency_id !== $agency->id) {
            abort(403);
        }

        $account->update(['is_active' => !$account->is_active]);

        return redirect()->route('social.accounts.index')->with('success', 'Account status updated.');
    }
}
