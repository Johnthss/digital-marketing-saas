<?php

namespace App\Http\Controllers;

use App\Models\SocialAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SocialAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'agency']);
    }

    public function index(Request $request)
    {
        $agencyId = $request->user()->agency_id;

        $accounts = SocialAccount::where('agency_id', $agencyId)
            ->orderBy('platform')
            ->get();

        $availablePlatforms = SocialAccount::SUPPORTED_PLATFORMS;

        return view('social.accounts.index', compact('agencyId', 'accounts', 'availablePlatforms'));
    }

    public function create(Request $request)
    {
        $agencyId = $request->user()->agency_id;
        $platforms = SocialAccount::SUPPORTED_PLATFORMS;

        return view('social.accounts.create', compact('agencyId', 'platforms'));
    }

    public function store(Request $request)
    {
        $agencyId = $request->user()->agency_id;

        $validated = $request->validate([
            'platform' => 'required|in:'.implode(',', array_keys(SocialAccount::SUPPORTED_PLATFORMS)),
            'access_token' => 'required|string',
            'refresh_token' => 'nullable|string',
            'platform_account_id' => 'nullable|string',
            'platform_username' => 'nullable|string',
            'platform_display_name' => 'nullable|string',
        ]);

        $account = SocialAccount::create([
            'agency_id' => $agencyId,
            'platform' => $validated['platform'],
            'access_token' => $validated['access_token'],
            'refresh_token' => $validated['refresh_token'] ?? null,
            'platform_account_id' => $validated['platform_account_id'] ?? null,
            'platform_username' => $validated['platform_username'] ?? null,
            'platform_display_name' => $validated['platform_display_name'] ?? null,
            'is_active' => true,
        ]);

        DB::table('agencies')->where('id', $agencyId)->increment('social_accounts_count');

        return redirect()->route('social.accounts.index')
            ->with('success', 'Social account connected successfully.');
    }

    public function destroy(Request $request, $accountId)
    {
        $agencyId = $request->user()->agency_id;

        $account = SocialAccount::findOrFail($accountId);

        if ($account->agency_id !== $agencyId) {
            abort(403);
        }

        $account->delete();
        DB::table('agencies')->where('id', $agencyId)->decrement('social_accounts_count');

        return redirect()->route('social.accounts.index')
            ->with('success', 'Social account removed.');
    }

    public function edit(Request $request, $accountId)
    {
        $account = SocialAccount::findOrFail($accountId);
        if ((int) $account->agency_id !== (int) $request->user()->agency_id) {
            abort(403);
        }

        return view('social.accounts.edit', compact('account'));
    }

    public function update(Request $request, $accountId)
    {
        $account = SocialAccount::findOrFail($accountId);
        if ((int) $account->agency_id !== (int) $request->user()->agency_id) {
            abort(403);
        }

        $validated = $request->validate([
            'access_token' => 'nullable|string',
            'refresh_token' => 'nullable|string',
            'platform_display_name' => 'nullable|string|max:255',
        ]);
        if (! $request->filled('access_token')) {
            unset($validated['access_token']);
        }
        if (! $request->filled('refresh_token')) {
            unset($validated['refresh_token']);
        }
        $account->update($validated);

        return redirect()->route('social.accounts.index')
            ->with('success', 'Social account updated successfully.');
    }

    public function toggle(Request $request, $accountId)
    {
        $agencyId = $request->user()->agency_id;

        $account = SocialAccount::findOrFail($accountId);

        if ($account->agency_id !== $agencyId) {
            abort(403);
        }

        $account->update(['is_active' => ! $account->is_active]);

        return redirect()->route('social.accounts.index')->with('success', 'Account status updated.');
    }
}
