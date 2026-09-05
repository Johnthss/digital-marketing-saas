@extends('layouts.app')
@section('title', 'Activity Log')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-history mr-2"></i>Activity Log</h3>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped">
            <thead><tr><th>Action</th><th>Description</th><th>User</th><th>Date</th></tr></thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td><span class="badge badge-info">{{ $log->action }}</span></td>
                        <td>{{ $log->description }}</td>
                        <td>{{ $log->user?->name ?? 'System' }}</td>
                        <td>{{ $log->created_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted">No activity logs</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
