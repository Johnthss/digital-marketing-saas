<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct() { $this->middleware(['auth', 'agency']); }

    public function index(Request $request)
    {
        $request->validate(['type' => 'nullable|in:social,email,campaign,analytics,custom']);
        $agency = $request->user()->agency;
        $type = $request->input('type');
        $query = Report::where('agency_id', $agency->id)->with('user');
        if ($type) $query->where('type', $type);
        $reports = $query->orderBy('created_at', 'desc')->paginate(20);
        $types = Report::where('agency_id', $agency->id)->select('type')->distinct()->pluck('type');
        return view('reports.index', compact('agency', 'reports', 'types'));
    }

    public function create(Request $request)
    {
        return view('reports.create', ['agency' => $request->user()->agency]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:social,email,campaign,analytics,custom',
            'format' => 'required|in:pdf,csv,xlsx',
            'schedule' => 'required|in:once,daily,weekly,monthly',
        ]);
        $agency = $request->user()->agency;
        Report::create([
            'agency_id' => $agency->id,
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'type' => $request->type,
            'format' => $request->format,
            'schedule' => $request->schedule,
            'filters' => $request->filters,
            'columns' => $request->columns,
            'status' => 'pending',
        ]);
        return redirect()->route('reports.index')->with('success', 'Report created. It will be generated shortly.');
    }

    public function show(Request $request, Report $report)
    {
        if ($report->agency_id !== $request->user()->agency->id) abort(403);
        return view('reports.show', ['agency' => $request->user()->agency, 'report' => $report]);
    }

    public function download(Request $request, Report $report)
    {
        if ($report->agency_id !== $request->user()->agency->id) abort(403);
        if (!$report->file_path) abort(404, 'Report file not found.');
        return response()->download(storage_path('app/public/' . $report->file_path));
    }

    public function destroy(Request $request, Report $report)
    {
        if ($report->agency_id !== $request->user()->agency->id) abort(403);
        $report->delete();
        return redirect()->route('reports.index')->with('success', 'Report deleted.');
    }

    public function generate(Request $request, Report $report)
    {
        if ($report->agency_id !== $request->user()->agency->id) abort(403);
        $report->update(['status' => 'processing']);
        return back()->with('success', 'Report generation started.');
    }
}
