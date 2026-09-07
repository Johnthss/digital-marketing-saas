<?php

namespace App\Http\Controllers;

use App\Models\ActivityFeed;
use Illuminate\Http\Request;

class ActivityFeedController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'agency']);
    }

    public function index(Request $request)
    {
        $agency = $request->user()->agency;

        $activities = ActivityFeed::where('agency_id', $agency->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return view('activity.index', compact('agency', 'activities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'action' => 'required|string|max:255',
            'subject_type' => 'required|string',
            'subject_id' => 'required|integer',
            'metadata' => 'nullable|array',
        ]);

        $agency = $request->user()->agency;

        $activity = ActivityFeed::create([
            'agency_id' => $agency->id,
            'user_id' => $request->user()->id,
            'action' => $request->action,
            'subject_type' => $request->subject_type,
            'subject_id' => $request->subject_id,
            'metadata' => $request->metadata,
        ]);

        return response()->json(['success' => true, 'activity' => $activity]);
    }
}
