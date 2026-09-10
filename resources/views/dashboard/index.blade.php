@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<!-- Stats Cards Row -->
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $stats['totalPosts'] ?? 0 }}</h3>
                <p>Total Posts</p>
            </div>
            <div class="icon"><i class="fas fa-pen-nib"></i></div>
            <a href="{{ route('social.posts.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $stats['publishedPosts'] ?? 0 }}</h3>
                <p>Published</p>
            </div>
            <div class="icon"><i class="fas fa-check-circle"></i></div>
            <a href="{{ route('social.posts.index', ['status' => 'published']) }}" class="small-box-footer">View <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $stats['scheduledPosts'] ?? 0 }}</h3>
                <p>Scheduled</p>
            </div>
            <div class="icon"><i class="fas fa-clock"></i></div>
            <a href="{{ route('social.posts.index', ['status' => 'scheduled']) }}" class="small-box-footer">View <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $stats['failedPosts'] ?? 0 }}</h3>
                <p>Failed</p>
            </div>
            <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
            <a href="{{ route('social.posts.index', ['status' => 'failed']) }}" class="small-box-footer">Fix <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $stats['totalCampaigns'] ?? 0 }}</h3>
                <p>Campaigns</p>
            </div>
            <div class="icon"><i class="fas fa-bullhorn"></i></div>
            <a href="{{ route('campaigns.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-teal">
            <div class="inner">
                <h3>{{ $stats['totalClients'] ?? 0 }}</h3>
                <p>Clients</p>
            </div>
            <div class="icon"><i class="fas fa-users"></i></div>
            <a href="{{ route('clients.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-indigo">
            <div class="inner">
                <h3>{{ $stats['totalInvoices'] ?? 0 }}</h3>
                <p>Invoices</p>
            </div>
            <div class="icon"><i class="fas fa-file-invoice-dollar"></i></div>
            <a href="{{ route('invoices.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-purple">
            <div class="inner">
                <h3>{{ $stats['aiGenerations'] ?? 0 }}</h3>
                <p>AI Generations</p>
            </div>
            <div class="icon"><i class="fas fa-robot"></i></div>
            <a href="{{ route('ai.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<!-- Quota Usage Row -->
@if(isset($quotas))
<div class="row">
    <div class="col-md-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-tachometer-alt mr-2"></i>Plan Usage</h3>
                <div class="card-tools">
                    <span class="badge badge-primary">{{ ucfirst($agency->subscription_plan ?? 'free') }} Plan</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($quotas as $key => $quota)
                    <div class="col-md-3">
                        <div class="progress-group">
                            <span class="progress-text">{{ $quota['label'] }}</span>
                            <span class="float-right"><b>{{ $quota['used'] }}</b>/{{ $quota['limit'] }}</span>
                            <div class="progress progress-sm">
                                <div class="progress-bar {{ $quota['percentage'] > 80 ? 'bg-danger' : ($quota['percentage'] > 50 ? 'bg-warning' : 'bg-primary') }}" style="width: {{ min($quota['percentage'], 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="row">
    <!-- Recent Activity -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-history mr-2"></i>Recent Activity</h3>
                <div class="card-tools">
                    <a href="{{ route('activity.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
            </div>
            <div class="card-body p-0">
                <ul class="products-list product-list-in-card pl-2 pr-2">
                    @forelse($recentActivity ?? [] as $activity)
                    <li class="item">
                        <div class="product-img">
                            <img src="{{ $activity->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($activity->user->name ?? 'U') }}" alt="Avatar" class="img-size-50 img-circle">
                        </div>
                        <div class="product-info">
                            <a href="#" class="product-title">{{ $activity->description ?? 'Activity' }}</a>
                            <span class="product-description"><i class="far fa-clock mr-1"></i>{{ $activity->created_at->diffForHumans() ?? 'Just now' }}</span>
                        </div>
                    </li>
                    @empty
                    <li class="item text-center py-4">
                        <span class="text-muted">No recent activity yet. Start by creating your first post!</span>
                    </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Upcoming -->
    <div class="col-md-4">
        <!-- Agent Health Widget -->
        @if(isset($agentHealthSummary) && $agentHealthSummary['total_agents'] > 0)
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-robot mr-2"></i>AI Agents</h3>
                <div class="card-tools">
                    <a href="{{ route('agents.dashboard') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>System Health:</span>
                    <span class="badge badge-{{ $agentHealthSummary['overall_status'] === 'healthy' ? 'success' : ($agentHealthSummary['overall_status'] === 'degraded' ? 'warning' : 'danger') }}">
                        {{ $agentHealthSummary['system_score'] }}%
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Healthy Agents:</span>
                    <span class="text-success font-weight-bold">{{ $agentHealthSummary['healthy_agents'] }}/{{ $agentHealthSummary['total_agents'] }}</span>
                </div>
                @if(!empty($recentAgentActivity) && $recentAgentActivity->count() > 0)
                <hr>
                <h6 class="text-muted">Recent Agent Activity</h6>
                <ul class="list-unstyled mb-0">
                    @foreach($recentAgentActivity as $activity)
                    <li class="d-flex justify-content-between align-items-center mb-1">
                        <small>{{ ucwords(str_replace('_', ' ', $activity->agent_name ?? 'Agent')) }}</small>
                        <small class="text-muted">{{ $activity->executed_at ? \Carbon\Carbon::parse($activity->executed_at)->diffForHumans() : '' }}</small>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
        </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-bolt mr-2"></i>Quick Actions</h3>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('social.posts.create') }}" class="btn btn-primary mb-2"><i class="fas fa-plus mr-1"></i> New Post</a>
                    <a href="{{ route('campaigns.create') }}" class="btn btn-success mb-2"><i class="fas fa-bullhorn mr-1"></i> New Campaign</a>
                    <a href="{{ route('clients.create') }}" class="btn btn-info mb-2"><i class="fas fa-user-plus mr-1"></i> New Client</a>
                    <a href="{{ route('invoices.create') }}" class="btn btn-warning mb-2"><i class="fas fa-file-invoice mr-1"></i> New Invoice</a>
                    <a href="{{ route('ai.index') }}" class="btn btn-purple mb-2"><i class="fas fa-robot mr-1"></i> AI Content</a>
                    <a href="{{ route('content.create') }}" class="btn btn-secondary mb-2"><i class="fas fa-folder-plus mr-1"></i> Add Content</a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-calendar-alt mr-2"></i>Upcoming Posts</h3>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($upcomingPosts ?? [] as $post)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge badge-{{ $post->platform_color }} mr-2">{{ ucfirst($post->socialAccount->platform ?? 'web') }}</span>
                            \Str::limit($post->content, 40)
                        </div>
                        <small class="text-muted">{{ $post->scheduled_at->diffForHumans() }}</small>
                    </li>
                    @empty
                    <li class="list-group-item text-center text-muted py-3">No scheduled posts</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
.gap-2 { gap: 0.5rem; }
.progress-group { margin-bottom: 1rem; }
</style>
@endpush
