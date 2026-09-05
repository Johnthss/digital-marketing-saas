@extends('layouts.app')
@section('title', 'Clients')
@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-users mr-2"></i>Clients</h3>
        <div class="card-tools">
            <a href="{{ route('clients.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus mr-1"></i> New Client</a>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped">
            <thead><tr><th>Name</th><th>Email</th><th>Company</th><th>Status</th><th>Campaigns</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($clients as $client)
                    <tr>
                        <td><a href="{{ route('clients.show', $client) }}">{{ $client->name }}</a></td>
                        <td>{{ $client->email }}</td>
                        <td>{{ $client->company ?? '—' }}</td>
                        <td><span class="badge badge-{{ $client->status === 'active' ? 'success' : ($client->status === 'lead' ? 'info' : 'secondary') }}">{{ ucfirst($client->status) }}</span></td>
                        <td>{{ $client->campaigns_count }}</td>
                        <td>
                            <a href="{{ route('clients.edit', $client) }}" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('clients.destroy', $client) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">No clients</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
