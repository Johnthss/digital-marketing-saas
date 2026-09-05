@extends('layouts.app')
@section('title', 'Analytics')

@section('content')
<div class="row">
    <div class="col-6 col-md-3">
        <div class="small-box bg-info">
            <div class="inner"><h3>{{ number_format($postStats['total_posts']) }}</h3><p>Total Posts</p></div>
            <div class="icon"><i class="fas fa-pen-fancy"></i></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="small-box bg-success">
            <div class="inner"><h3>{{ number_format($engagement->total_likes ?? 0) }}</h3><p>Total Likes</p></div>
            <div class="icon"><i class="fas fa-heart"></i></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="small-box bg-warning">
            <div class="inner"><h3>{{ number_format($engagement->total_shares ?? 0) }}</h3><p>Total Shares</p></div>
            <div class="icon"><i class="fas fa-share-alt"></i></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="small-box bg-danger">
            <div class="inner"><h3>${{ number_format($revenueStats['paid'], 2) }}</h3><p>Revenue (Paid)</p></div>
            <div class="icon"><i class="fas fa-dollar-sign"></i></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Engagement Overview</h3></div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr><td><i class="fas fa-eye text-info"></i> Total Views</td><td class="text-right"><strong>{{ number_format($engagement->total_views ?? 0) }}</strong></td></tr>
                    <tr><td><i class="fas fa-heart text-danger"></i> Total Likes</td><td class="text-right"><strong>{{ number_format($engagement->total_likes ?? 0) }}</strong></td></tr>
                    <tr><td><i class="fas fa-comment text-primary"></i> Total Comments</td><td class="text-right"><strong>{{ number_format($engagement->total_comments ?? 0) }}</strong></td></tr>
                    <tr><td><i class="fas fa-share text-success"></i> Total Shares</td><td class="text-right"><strong>{{ number_format($engagement->total_shares ?? 0) }}</strong></td></tr>
                    <tr><td><i class="fas fa-mouse-pointer text-warning"></i> Total Clicks</td><td class="text-right"><strong>{{ number_format($engagement->total_clicks ?? 0) }}</strong></td></tr>
                    <tr><td><i class="fas fa-chart-line text-info"></i> Avg Quality Score</td><td class="text-right"><strong>{{ number_format($postStats['avg_quality_score'], 1) }}</strong></td></tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Platform Breakdown</h3></div>
            <div class="card-body">
                @forelse($platformStats as $platform => $stat)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>{{ ucfirst($platform) }}</span>
                            <strong>{{ $stat->total }} posts</strong>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-{{ $loop->iteration % 2 == 0 ? 'success' : 'primary' }}" style="width: {{ min(100, ($stat->total / max(1, $postStats['total_posts'])) * 100) }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted text-center">No data available</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card card-outline card-primary">
            <div class="card-header"><h3 class="card-title">Campaigns</h3></div>
            <div class="card-body">
                <div class="d-flex justify-content-between"><span>Active</span><strong>{{ $campaignStats['active'] }}</strong></div>
                <div class="d-flex justify-content-between"><span>Completed</span><strong>{{ $campaignStats['completed'] }}</strong></div>
                <div class="d-flex justify-content-between"><span>Total</span><strong>{{ $campaignStats['total'] }}</strong></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-outline card-success">
            <div class="card-header"><h3 class="card-title">Clients</h3></div>
            <div class="card-body">
                <div class="d-flex justify-content-between"><span>Active</span><strong>{{ $clientStats['active'] }}</strong></div>
                <div class="d-flex justify-content-between"><span>Leads</span><strong>{{ $clientStats['leads'] }}</strong></div>
                <div class="d-flex justify-content-between"><span>Total</span><strong>{{ $clientStats['total'] }}</strong></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-outline card-warning">
            <div class="card-header"><h3 class="card-title">AI Usage</h3></div>
            <div class="card-body">
                <div class="d-flex justify-content-between"><span>Generations</span><strong>{{ $aiStats['total_generations'] }}</strong></div>
                <div class="d-flex justify-content-between"><span>Successful</span><strong>{{ $aiStats['successful'] }}</strong></div>
                <div class="d-flex justify-content-between"><span>Cost</span><strong>${{ number_format($aiStats['total_cost'], 4) }}</strong></div>
                <div class="d-flex justify-content-between"><span>Tokens</span><strong>{{ number_format($aiStats['total_tokens']) }}</strong></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Top 5 Performing Posts</h3></div>
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead><tr><th>Platform</th><th>Content</th><th>Likes</th><th>Comments</th><th>Shares</th><th>Published</th></tr></thead>
                    <tbody>
                        @forelse($bestPosts as $post)
                            <tr>
                                <td><span class="badge badge-info">{{ ucfirst($post->platform) }}</span></td>
                                <td>{{ Str::limit($post->content, 50) }}</td>
                                <td>{{ $post->likes_count }}</td>
                                <td>{{ $post->comments_count }}</td>
                                <td>{{ $post->shares_count }}</td>
                                <td>{{ $post->published_at?->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">No published posts yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
