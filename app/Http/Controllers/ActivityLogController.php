<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'agency']);
    }

    public function index(Request $request)
    {
        $agencyId = $request->user()->agency_id;
        $query = ActivityLog::where('agency_id', $agencyId);

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->with('user')->orderBy('created_at', 'desc')->paginate(25);

        $actions = ActivityLog::where('agency_id', $agencyId)
            ->select('action')
            ->distinct()
            ->pluck('action');

        return view('activity.index', compact('logs', 'actions'));
    }

    public function show(Request $request, $id)
    {
        $agencyId = $request->user()->agency_id;
        $log = ActivityLog::findOrFail($id);

        if ((int) $log->agency_id !== (int) $agencyId) {
            abort(403);
        }

        return view('activity.show', compact('log'));
    }
}
