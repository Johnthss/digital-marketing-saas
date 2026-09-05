@extends('layouts.app')
@section('title', 'Inbox')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-inbox mr-2"></i>Messages</h3>
                <div class="card-tools">
                    <span class="badge badge-danger">{{ $unreadCount }} unread</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($messages as $msg)
                        <a href="{{ route('inbox.show', $msg) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">
                                    @if($msg->status === 'unread')
                                        <span class="badge badge-danger mr-1">●</span>
                                    @endif
                                    {{ $msg->author_name }}
                                    <small class="text-muted">{{ '@' . ($msg->author_username ?? 'user') }}</small>
                                </h6>
                                <small>{{ $msg->received_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-1 text-truncate">{{ Str::limit($msg->content, 80) }}</p>
                            <small>
                                <span class="badge badge-info">{{ ucfirst($msg->platform) }}</span>
                                <span class="badge badge-{{ $msg->status === 'unread' ? 'danger' : ($msg->status === 'replied' ? 'success' : 'secondary') }}">{{ ucfirst($msg->status) }}</span>
                            </small>
                        </a>
                    @empty
                        <div class="list-group-item text-center text-muted">No messages</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-body text-center text-muted">
                <i class="fas fa-envelope-open fa-3x mb-3"></i>
                <p>Select a message to view details</p>
            </div>
        </div>
    </div>
</div>
@endsection
