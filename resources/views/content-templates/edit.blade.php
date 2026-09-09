@extends('layouts.app')
@section('title', 'Edit Template')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1>Edit: {{ $template->name }}</h1>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <form action="{{ route('content-templates.update', $template) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Name *</label>
                                    <input type="text" name="name" class="form-control" value="{{ $template->name }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Platform *</label>
                                    <select name="platform" class="form-control" required>
                                        @foreach($platforms as $platform)
                                        <option value="{{ $platform }}" {{ $template->platform === $platform ? 'selected' : '' }}>{{ ucfirst($platform) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Type *</label>
                                    <select name="type" class="form-control" required>
                                        @foreach($types as $type)
                                        <option value="{{ $type }}" {{ $template->type === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Content *</label>
                                    <textarea name="template_content" class="form-control" rows="10" required>{{ $template->template_content }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="active" {{ $template->status === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ $template->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Update Template</button>
                                <a href="{{ route('content-templates.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
