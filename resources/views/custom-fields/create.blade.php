@extends('layouts.app')
@section('title', 'Create Custom Field')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1>Create Custom Field</h1>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <form action="{{ route('custom-fields.store') }}" method="POST">
                        @csrf
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Name *</label>
                                    <input type="text" name="name" class="form-control" required placeholder="Field name">
                                </div>
                                <div class="form-group">
                                    <label>Type *</label>
                                    <select name="type" class="form-control" required>
                                        @foreach($types as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Model Type *</label>
                                    <input type="text" name="model_type" class="form-control" required placeholder="App\Models\Client">
                                </div>
                                <div class="form-group">
                                    <label>Options (for select type)</label>
                                    <textarea name="options" class="form-control" rows="3" placeholder="One option per line"></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Sort Order</label>
                                    <input type="number" name="sort_order" class="form-control" value="0">
                                </div>
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" name="is_required" value="1"> Required
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" name="is_active" value="1" checked> Active
                                    </label>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Save Field</button>
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
