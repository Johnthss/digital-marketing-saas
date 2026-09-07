<?php

namespace App\Http\Controllers;

use App\Models\WhiteLabelSetting;
use Illuminate\Http\Request;

class WhiteLabelController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'agency']);
    }

    public function index(Request $request)
    {
        $agency = $request->user()->agency;
        $settings = WhiteLabelSetting::where('agency_id', $agency->id)->first();

        return view('white-label.index', compact('agency', 'settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'brand_name' => 'nullable|string|max:255',
            'brand_color' => 'nullable|string|max:7',
            'logo_url' => 'nullable|url|max:2048',
            'favicon_url' => 'nullable|url|max:2048',
            'from_name' => 'nullable|string|max:255',
            'from_email' => 'nullable|email|max:255',
            'custom_css' => 'nullable|string|max:10000',
            'email_signature' => 'nullable|string|max:5000',
            'hide_powered_by' => 'boolean',
            'enabled' => 'boolean',
        ]);

        $agency = $request->user()->agency;

        WhiteLabelSetting::updateOrCreate(
            ['agency_id' => $agency->id],
            $request->only([
                'brand_name',
                'brand_color',
                'logo_url',
                'favicon_url',
                'from_name',
                'from_email',
                'custom_css',
                'email_signature',
                'hide_powered_by',
                'enabled',
            ])
        );

        return back()->with('success', 'White-label settings updated.');
    }
}
