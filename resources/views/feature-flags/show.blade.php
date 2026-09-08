@extends('layouts.app')
@section('title', 'Feature: {{ $flag->feature_name }}')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between">
                <h1>{{ $flag->feature_name }}</h1>
                <a href="{{ route('feature-flags.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <p><strong>Key:</strong> <code>{{ $flag->feature_key }}</code></p>
                    <p><strong>Description:</strong> {{ $flag->description ?? '—' }}</p>
                    <p><strong>Enabled:</strong> <span class="badge badge-{{ $flag->enabled ? 'success' : 'secondary' }}">{{ $flag->enabled ? 'On' : 'Off' }}</span></p>
                </div>
                <div class="card-footer">
                    <a href="{{ route('feature-flags.edit', $flag) }}" class="btn btn-warning">Edit</a>
                    <form action="{{ route('feature-flags.destroy', $flag) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Delete?')">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
