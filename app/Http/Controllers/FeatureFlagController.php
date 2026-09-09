<?php

namespace App\Http\Controllers;

use App\Models\FeatureFlag;
use Illuminate\Http\Request;

class FeatureFlagController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'agency']);
    }

    public function index(Request $request)
    {
        $agency = $request->user()->agency;
        $flags = FeatureFlag::where('agency_id', $agency->id)
            ->orderBy('feature_name')
            ->paginate(20);

        return view('feature-flags.index', compact('flags'));
    }

    public function create()
    {
        return view('feature-flags.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'feature_key' => 'required|string|max:255',
            'feature_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'enabled' => 'boolean',
            'required_plan' => 'nullable|string',
            'minimum_version' => 'nullable|string',
        ]);

        $agency = $request->user()->agency;
        $existing = FeatureFlag::where('agency_id', $agency->id)
            ->where('feature_key', $data['feature_key'])
            ->first();

        if ($existing) {
            return back()->withErrors(['feature_key' => 'Feature flag already exists.']);
        }

        $agency->featureFlags()->create($data + ['enabled' => $data['enabled'] ?? true]);

        return redirect()->route('feature-flags.index')->with('success', 'Feature flag created.');
    }

    public function show(Request $request, FeatureFlag $flag)
    {
        $agency = $request->user()->agency;
        if ($flag->agency_id !== $agency->id) {
            abort(403);
        }

        return view('feature-flags.show', compact('flag'));
    }

    public function edit(Request $request, FeatureFlag $flag)
    {
        $agency = $request->user()->agency;
        if ($flag->agency_id !== $agency->id) {
            abort(403);
        }

        return view('feature-flags.edit', compact('flag'));
    }

    public function update(Request $request, FeatureFlag $flag)
    {
        $agency = $request->user()->agency;
        if ($flag->agency_id !== $agency->id) {
            abort(403);
        }

        $data = $request->validate([
            'feature_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'enabled' => 'boolean',
            'required_plan' => 'nullable|string',
            'minimum_version' => 'nullable|string',
        ]);

        $flag->update($data + ['enabled' => $data['enabled'] ?? true]);

        return redirect()->route('feature-flags.index')->with('success', 'Feature flag updated.');
    }

    public function destroy(Request $request, FeatureFlag $flag)
    {
        $agency = $request->user()->agency;
        if ($flag->agency_id !== $agency->id) {
            abort(403);
        }

        $flag->delete();

        return redirect()->route('feature-flags.index')->with('success', 'Feature flag deleted.');
    }
}
