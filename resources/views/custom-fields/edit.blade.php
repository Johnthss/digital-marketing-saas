@extends('layouts.app')
@section('title', 'Edit Custom Field')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1>Edit: {{ $field->name }}</h1>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <form action="{{ route('custom-fields.update', $field) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Name *</label>
                                    <input type="text" name="name" class="form-control" value="{{ $field->name }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Type *</label>
                                    <select name="type" class="form-control" required>
                                        @foreach($types as $key => $label)
                                        <option value="{{ $key }}" {{ $field->type === $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Model Type *</label>
                                    <input type="text" name="model_type" class="form-control" value="{{ $field->model_type }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Sort Order</label>
                                    <input type="number" name="sort_order" class="form-control" value="{{ $field->sort_order ?? 0 }}">
                                </div>
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" name="is_required" value="1" {{ $field->is_required ? 'checked' : '' }}> Required
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" name="is_active" value="1" {{ $field->is_active ? 'checked' : '' }}> Active
                                    </label>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Update Field</button>
                                <a href="{{ route('custom-fields.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
