@extends('layouts.app')
@section('title', 'Edit Feature Flag')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1>Edit: {{ $flag->feature_name }}</h1>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <form action="{{ route('feature-flags.update', $flag) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Feature Key</label>
                                    <input type="text" class="form-control" value="{{ $flag->feature_key }}" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Feature Name *</label>
                                    <input type="text" name="feature_name" class="form-control" value="{{ $flag->feature_name }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea name="description" class="form-control" rows="3">{{ $flag->description }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label>Enabled</label>
                                    <select name="enabled" class="form-control">
                                        <option value="1" {{ $flag->enabled ? 'selected' : '' }}>On</option>
                                        <option value="0" {{ !$flag->enabled ? 'selected' : '' }}>Off</option>
                                    </select>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Update Flag</button>
                                <a href="{{ route('feature-flags.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
