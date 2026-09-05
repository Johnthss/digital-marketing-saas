@extends('layouts.app')
@section('title', 'Campaigns')
@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-bullhorn mr-2"></i>Campaigns</h3>
        <div class="card-tools">
            <a href="{{ route('campaigns.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus mr-1"></i> New Campaign</a>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped">
            <thead><tr><th>Name</th><th>Type</th><th>Status</th><th>Client</th><th>Dates</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($campaigns as $campaign)
                    <tr>
                        <td><a href="{{ route('campaigns.show', $campaign) }}">{{ $campaign->name }}</a></td>
                        <td>{{ \App\Models\Campaign::CAMPAIGN_TYPES[$campaign->type] ?? $campaign->type }}</td>
                        <td><span class="badge badge-{{ $campaign->status === 'active' ? 'success' : ($campaign->status === 'draft' ? 'secondary' : 'warning') }}">{{ ucfirst($campaign->status) }}</span></td>
                        <td>{{ $campaign->client?->name ?? '—' }}</td>
                        <td><small>{{ $campaign->start_date?->format('M d') }} - {{ $campaign->end_date?->format('M d, Y') }}</small></td>
                        <td>
                            <a href="{{ route('campaigns.edit', $campaign) }}" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('campaigns.destroy', $campaign) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">No campaigns</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
