@extends('layouts.app')
@section('title', 'Workflows')
@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-project-diagram mr-2"></i>Workflows</h3>
        <div class="card-tools">
            <a href="{{ route('workflows.builder') }}" class="btn btn-success btn-sm mr-2"><i class="fas fa-magic mr-1"></i> Visual Builder</a>
            <a href="{{ route('workflows.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus mr-1"></i> New Workflow</a>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped">
            <thead><tr><th>Name</th><th>Trigger</th><th>Status</th><th>Executions</th><th>Last Run</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($workflows as $wf)
                    <tr>
                        <td><a href="{{ route('workflows.show', $wf) }}">{{ $wf->name }}</a></td>
                        <td>{{ \App\Models\Workflow::TRIGGER_TYPES[$wf->trigger_type] ?? $wf->trigger_type }}</td>
                        <td><span class="badge badge-{{ $wf->status === 'active' ? 'success' : ($wf->status === 'paused' ? 'warning' : 'secondary') }}">{{ ucfirst($wf->status) }}</span></td>
                        <td>{{ $wf->execution_count }}</td>
                        <td>{{ $wf->last_executed_at?->diffForHumans() ?? 'Never' }}</td>
                        <td>
                            <form action="{{ route('workflows.toggle', $wf) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-xs btn-{{ $wf->status === 'active' ? 'warning' : 'success' }}">{{ $wf->status === 'active' ? 'Pause' : 'Activate' }}</button>
                            </form>
                            <a href="{{ route('workflows.edit', $wf) }}" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('workflows.destroy', $wf) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">No workflows</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
