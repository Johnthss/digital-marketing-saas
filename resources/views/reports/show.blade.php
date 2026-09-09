@extends('layouts.app')
@section('title', 'Report: {{ $report->name }}')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1>{{ $report->name }}</h1>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <p><strong>Type:</strong> {{ ucfirst($report->type) }}</p>
                    <p><strong>Format:</strong> {{ strtoupper($report->format) }}</p>
                    <p><strong>Schedule:</strong> {{ ucfirst($report->schedule) }}</p>
                    <p><strong>Status:</strong> <span class="badge badge-{{ $report->status === 'completed' ? 'success' : ($report->status === 'processing' ? 'warning' : 'secondary') }}">{{ ucfirst($report->status) }}</span></p>
                </div>
                <div class="card-footer">
                    <a href="{{ route('reports.download', $report) }}" class="btn btn-sm btn-info">Download</a>
                    <form action="{{ route('reports.destroy', $report) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
