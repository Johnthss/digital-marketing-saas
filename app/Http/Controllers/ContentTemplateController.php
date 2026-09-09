<?php

namespace App\Http\Controllers;

use App\Models\ContentTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ContentTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'agency']);
    }

    public function index(Request $request)
    {
        $agency = $request->user()->agency;
        $query = ContentTemplate::where('agency_id', $agency->id);

        if ($request->filled('platform')) {
            $query->where('platform', $request->platform);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $templates = $query->orderBy('created_at', 'desc')->paginate(20);
        $platforms = ContentTemplate::PLATFORMS ?? ['twitter', 'facebook', 'instagram', 'linkedin', 'tiktok', 'pinterest'];
        $types = ContentTemplate::TYPES ?? ['post', 'story', 'reel', 'pin', 'article'];

        return view('content-templates.index', compact('templates', 'platforms', 'types'));
    }

    public function create()
    {
        $platforms = ContentTemplate::PLATFORMS ?? ['twitter', 'facebook', 'instagram', 'linkedin', 'tiktok', 'pinterest'];
        $types = ContentTemplate::TYPES ?? ['post', 'story', 'reel', 'pin', 'article'];

        return view('content-templates.create', compact('platforms', 'types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'platform' => 'required|string|max:50',
            'type' => 'required|string|max:50',
            'template_content' => 'required|string',
            'variables' => 'nullable|array',
            'hashtags' => 'nullable|array',
            'status' => 'required|in:active,inactive',
        ]);

        $agency = $request->user()->agency;
        $template = ContentTemplate::create([
            'agency_id' => $agency->id,
            'slug' => Str::slug($validated['name']).'-'.uniqid(),
            ...$validated,
        ]);

        return redirect()->route('content-templates.show', $template)
            ->with('success', 'Template created.');
    }

    public function show(Request $request, ContentTemplate $template)
    {
        $agency = $request->user()->agency;
        if ($template->agency_id !== $agency->id) {
            abort(403);
        }

        return view('content-templates.show', compact('template'));
    }

    public function edit(Request $request, ContentTemplate $template)
    {
        $agency = $request->user()->agency;
        if ($template->agency_id !== $agency->id) {
            abort(403);
        }

        $platforms = ContentTemplate::PLATFORMS ?? ['twitter', 'facebook', 'instagram', 'linkedin', 'tiktok', 'pinterest'];
        $types = ContentTemplate::TYPES ?? ['post', 'story', 'reel', 'pin', 'article'];

        return view('content-templates.edit', compact('template', 'platforms', 'types'));
    }

    public function update(Request $request, ContentTemplate $template)
    {
        $agency = $request->user()->agency;
        if ($template->agency_id !== $agency->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'platform' => 'required|string|max:50',
            'type' => 'required|string|max:50',
            'template_content' => 'required|string',
            'variables' => 'nullable|array',
            'hashtags' => 'nullable|array',
            'status' => 'required|in:active,inactive',
        ]);

        $template->update($validated);

        return redirect()->route('content-templates.show', $template)
            ->with('success', 'Template updated.');
    }

    public function destroy(Request $request, ContentTemplate $template)
    {
        $agency = $request->user()->agency;
        if ($template->agency_id !== $agency->id) {
            abort(403);
        }

        $template->delete();

        return redirect()->route('content-templates.index')
            ->with('success', 'Template deleted.');
    }
}
