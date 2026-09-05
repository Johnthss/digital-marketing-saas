@extends('layouts.app')
@section('title', 'Social Accounts')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-share-alt mr-2"></i>Connected Social Accounts</h3>
                <div class="card-tools">
                    <a href="{{ route('social.accounts.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i> Connect Account
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse($accounts as $account)
                        <div class="col-md-4 col-lg-3">
                            <div class="card card-outline card-{{ $account->is_active ? 'success' : 'secondary' }}">
                                <div class="card-body box-profile">
                                    <div class="text-center">
                                        @switch($account->platform)
                                            @case('facebook')
                                                <i class="fab fa-facebook fa-3x text-primary"></i>
                                                @break
                                            @case('instagram')
                                                <i class="fab fa-instagram fa-3x text-danger"></i>
                                                @break
                                            @case('twitter')
                                                <i class="fab fa-twitter fa-3x text-info"></i>
                                                @break
                                            @case('linkedin')
                                                <i class="fab fa-linkedin fa-3x text-primary"></i>
                                                @break
                                            @case('tiktok')
                                                <i class="fab fa-tiktok fa-3x text-dark"></i>
                                                @break
                                            @case('pinterest')
                                                <i class="fab fa-pinterest fa-3x text-danger"></i>
                                                @break
                                        @endswitch
                                    </div>
                                    <h3 class="profile-username text-center">{{ $account->platform_display_name ?? ucfirst($account->platform) }}</h3>
                                    <p class="text-muted text-center">{{ $account->platform_username ?? 'Connected' }}</p>
                                    <ul class="list-group list-group-unbordered mb-3">
                                        <li class="list-group-item">
                                            <b>Status</b>
                                                    <span class="float-right badge badge-{{ $account->is_active ? 'success' : 'secondary' }}">{{ $account->is_active ? 'Active' : 'Inactive' }}</span>
                                                </li>
                                                <li class="list-group-item">
                                                    <b>Posts</b> <span class="float-right">{{ $account->posts_count ?? 0 }}</span>
                                                </li>
                                            </ul>
                                            <form action="{{ route('social.accounts.toggle', $account) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-block btn-{{ $account->is_active ? 'warning' : 'success' }}">
                                                    <i class="fas fa-power-off mr-1"></i> {{ $account->is_active ? 'Disable' : 'Enable' }}
                                                </button>
                                            </form>
                                            <form action="{{ route('social.accounts.destroy', $account) }}" method="POST" class="d-inline mt-1">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-block btn-danger"
                                                        onclick="return confirm('Remove this account?')">
                                                    <i class="fas fa-trash mr-1"></i> Remove
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center text-muted">
                                    <i class="fas fa-share-alt fa-3x mb-3"></i>
                                    <p>No social accounts connected yet.</p>
                                    <a href="{{ route('social.accounts.create') }}" class="btn btn-primary">Connect Your First Account</a>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
