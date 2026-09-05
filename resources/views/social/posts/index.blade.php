@extends('layouts.app')
@section('title', 'Posts')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-pen-fancy mr-2"></i>Posts</h3>
        <div class="card-tools">
            <a href="{{ route('social.posts.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus mr-1"></i> New Post</a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="mb-3">
            <div class="row">
                <div class="col-md-3">
                    <select name="status" class="form-control form-control-sm">
                        <option value="">All Status</option>
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="platform" class="form-control form-control-sm">
                        <option value="">All Platforms</option>
                        @foreach($platforms as $key => $label)
                            <option value="{{ $key }}" {{ request('platform') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search content..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-secondary btn-block">Filter</button>
                </div>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Platform</th>
                        <th>Content</th>
                        <th>Status</th>
                        <th>Scheduled</th>
                        <th>Quality</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($posts as $post)
                        <tr>
                            <td>{{ $post->id }}</td>
                            <td><span class="badge badge-info">{{ ucfirst($post->platform) }}</span></td>
                            <td>{{ Str::limit($post->content, 60) }}</td>
                            <td><span class="badge badge-{{ $post->status === 'published' ? 'success' : ($post->status === 'failed' ? 'danger' : ($post->status === 'scheduled' ? 'warning' : 'secondary')) }}">{{ ucfirst($post->status) }}</span></td>
                            <td>{{ $post->scheduled_at ? $post->scheduled_at->format('M d, Y H:i') : '—' }}</td>
                            <td>
                                @if($post->quality_score)
                                    <span class="badge badge-{{ $post->quality_score >= 60 ? 'success' : 'warning' }}">{{ $post->quality_score }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('social.posts.show', $post) }}" class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('social.posts.edit', $post) }}" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a>
                                @if($post->status === 'draft' || $post->status === 'scheduled')
                                    <form action="{{ route('social.posts.publish', $post) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-xs btn-success"><i class="fas fa-paper-plane"></i></button>
                                    </form>
                                @endif
                                @if($post->status === 'failed')
                                    <form action="{{ route('social.posts.retry', $post) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-xs btn-warning"><i class="fas fa-redo"></i></button>
                                    </form>
                                @endif
                                <form action="{{ route('social.posts.destroy', $post) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">No posts found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $posts->appends(request()->query())->links() }}</div>
    </div>
</div>
@endsection
