<?php

namespace App\Http\Controllers;

use App\Models\CustomField;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CustomFieldController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'agency']);
    }

    public function index(Request $request)
    {
        $agency = $request->user()->agency;
        $fields = CustomField::where('agency_id', $agency->id)
            ->orderBy('sort_order')
            ->paginate(20);

        return view('custom-fields.index', compact('fields'));
    }

    public function create()
    {
        $types = CustomField::FIELD_TYPES;
        return view('custom-fields.create', compact('types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:text,number,date,select,textarea,boolean',
            'model_type' => 'required|string|max:255',
            'options' => 'nullable|array',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $agency = $request->user()->agency;
        $field = CustomField::create([
            'agency_id' => $agency->id,
            'slug' => Str::slug($validated['name']).'-'.uniqid(),
            ...$validated,
        ]);

        return redirect()->route('custom-fields.show', $field)
            ->with('success', 'Custom field created.');
    }

    public function show(Request $request, CustomField $field)
    {
        $agency = $request->user()->agency;
        if ($field->agency_id !== $agency->id) {
            abort(403);
        }

        return view('custom-fields.show', compact('field'));
    }

    public function edit(Request $request, CustomField $field)
    {
        $agency = $request->user()->agency;
        if ($field->agency_id !== $agency->id) {
            abort(403);
        }

        $types = CustomField::FIELD_TYPES;
        return view('custom-fields.edit', compact('field', 'types'));
    }

    public function update(Request $request, CustomField $field)
    {
        $agency = $request->user()->agency;
        if ($field->agency_id !== $agency->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:text,number,date,select,textarea,boolean',
            'model_type' => 'required|string|max:255',
            'options' => 'nullable|array',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $field->update($validated);

        return redirect()->route('custom-fields.show', $field)
            ->with('success', 'Custom field updated.');
    }

    public function destroy(Request $request, CustomField $field)
    {
        $agency = $request->user()->agency;
        if ($field->agency_id !== $agency->id) {
            abort(403);
        }

        $field->delete();

        return redirect()->route('custom-fields.index')
            ->with('success', 'Custom field deleted.');
    }
}
