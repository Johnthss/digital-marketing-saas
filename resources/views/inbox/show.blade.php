@extends('layouts.app')
@section('title', 'Message from ' . $message->author_name)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($message->author_name) }}&background=random&size=32" class="img-circle mr-2" width="32">
                    {{ $message->author_name }}
                    <small class="text-muted">{{ '@' . ($message->author_username ?? 'user') }}</small>
                </h3>
                <div class="card-tools">
                    <span class="badge badge-info">{{ ucfirst($message->platform) }}</span>
                    <span class="badge badge-{{ $message->status === 'replied' ? 'success' : 'secondary' }}">{{ ucfirst($message->status) }}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="direct-chat-msg">
                    <div class="direct-chat-infos clearfix">
                        <span class="direct-chat-name float-left">{{ $message->author_name }}</span>
                        <span class="direct-chat-timestamp float-right">{{ $message->received_at->diffForHumans() }}</span>
                    </div>
                    <img class="direct-chat-img" src="https://ui-avatars.com/api/?name={{ urlencode($message->author_name) }}&background=random" alt="">
                    <div class="direct-chat-text">
                        {{ $message->content }}
                    </div>
                </div>

                @if($message->replied_content)
                    <div class="direct-chat-msg right">
                        <div class="direct-chat-infos clearfix">
                            <span class="direct-chat-name float-right">{{ $message->replier?->name ?? 'You' }}</span>
                            <span class="direct-chat-timestamp float-left">{{ $message->replied_at?->diffForHumans() }}</span>
                        </div>
                        <img class="direct-chat-img" src="https://ui-avatars.com/api/?name={{ urlencode($message->replier?->name ?? 'Me') }}&background=random" alt="">
                        <div class="direct-chat-text" style="background: #007bff; color: #fff;">
                            {{ $message->replied_content }}
                        </div>
                    </div>
                @endif
            </div>
            @if($message->status !== 'replied')
                <div class="card-footer">
                    <form action="{{ route('inbox.reply', $message) }}" method="POST">
                        @csrf
                        <div class="input-group">
                            <input type="text" name="reply_content" class="form-control" placeholder="Type your reply..." required>
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Details</h3></div>
            <div class="card-body">
                <strong><i class="fas fa-tag mr-1"></i> Type</strong><p class="text-muted">{{ \App\Models\InboxMessage::MESSAGE_TYPES[$message->message_type] ?? $message->message_type }}</p><hr>
                <strong><i class="fas fa-share-alt mr-1"></i> Platform</strong><p class="text-muted">{{ ucfirst($message->platform) }}</p><hr>
                <strong><i class="fas fa-clock mr-1"></i> Received</strong><p class="text-muted">{{ $message->received_at->format('M d, Y H:i') }}</p><hr>
                @if($message->triage)
                    <strong><i class="fas fa-brain mr-1"></i> AI Analysis</strong><p class="text-muted">{{ $message->triage->ai_analysis }}</p><hr>
                    <strong><i class="fas fa-smile mr-1"></i> Sentiment</strong><span class="badge badge-{{ $message->triage->sentiment === 'positive' ? 'success' : ($message->triage->sentiment === 'negative' ? 'danger' : 'secondary') }}">{{ ucfirst($message->triage->sentiment ?? 'unknown') }}</span><hr>
                @endif
            </div>
            <div class="card-footer">
                <form action="{{ route('inbox.triage', $message) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <select name="action" class="form-control form-control-sm">
                            <option value="">Triage Action...</option>
                            @foreach(\App\Models\InboxTriage::ACTIONS as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-sm btn-block btn-info"><i class="fas fa-robot mr-1"></i> Triage</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
