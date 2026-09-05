@extends('layouts.app')
@section('title', 'Content Library')
@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-folder-open mr-2"></i>Content Library</h3>
        <div class="card-tools">
            <a href="{{ route('content.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus mr-1"></i> New Asset</a>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped">
            <thead><tr><th>Name</th><th>Type</th><th>Tags</th><th>Usage</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($assets as $asset)
                    <tr>
                        <td><a href="{{ route('content.show', $asset) }}">{{ $asset->name }}</a></td>
                        <td><span class="badge badge-info">{{ \App\Models\ContentAsset::ASSET_TYPES[$asset->type] ?? $asset->type }}</span></td>
                        <td>@foreach($asset->tags ?? [] as $tag)<span class="badge badge-secondary mr-1">{{ $tag }}</span>@endforeach</td>
                        <td>{{ $asset->usage_count }}</td>
                        <td><span class="badge badge-{{ $asset->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($asset->status) }}</span></td>
                        <td>
                            <a href="{{ route('content.edit', $asset) }}" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('content.destroy', $asset) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">No content assets</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
