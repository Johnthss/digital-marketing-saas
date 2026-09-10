<?php

namespace App\Http\Controllers;

use App\Models\ActivityFeed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityFeedController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'agency']);
    }

    public function index(Request $request)
    {
        $agencyId = $request->user()->agency_id;
        $query = ActivityFeed::where('agency_id', $agencyId)
            ->with('user')
            ->orderBy('created_at', 'desc');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $activities = $query->paginate(20);

        return view('activity-feed.index', compact('activities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'subject_type' => 'nullable|string|max:255',
            'subject_id' => 'nullable|integer',
            'metadata' => 'nullable|array',
        ]);

        ActivityFeed::create([
            'agency_id' => $request->user()->agency_id,
            'user_id' => $request->user()->id,
            ...$validated,
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy(Request $request, $id)
    {
        $activity = DB::table('activity_feed')->where('id', $id)->first();

        if (! $activity || (int) $activity->agency_id !== (int) $request->user()->agency_id) {
            abort(403);
        }

        ActivityFeed::where('id', $id)->delete();

        return response()->json(['success' => true]);
    }
}
