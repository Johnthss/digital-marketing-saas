@extends('layouts.app')
@section('title', 'Custom Fields')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <h1>Custom Fields</h1>
                <a href="{{ route('custom-fields.create') }}" class="btn btn-primary">Create Field</a>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Model</th>
                                <th>Required</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($fields as $field)
                            <tr>
                                <td>{{ $field->name }}</td>
                                <td><span class="badge badge-info">{{ $types[$field->type] ?? $field->type }}</span></td>
                                <td>{{ $field->model_type }}</td>
                                <td>
                                    <span class="badge badge-{{ $field->is_required ? 'danger' : 'secondary' }}">
                                        {{ $field->is_required ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $field->is_active ? 'success' : 'secondary' }}">
                                        {{ $field->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('custom-fields.show', $field) }}" class="btn btn-sm btn-info">View</a>
                                    <a href="{{ route('custom-fields.edit', $field) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('custom-fields.destroy', $field) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center">No custom fields found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{ $fields->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
