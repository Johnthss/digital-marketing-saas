@extends('layouts.app')
@section('title', 'Edit Account')
@section('content')
<div class="row"><div class="col-md-8"><div class="card"><div class="card-header"><h3 class="card-title">Edit Social Account</h3></div>
    <form action="{{ route('social.accounts.update', $account) }}" method="POST">@csrf @method('PUT')
        <div class="card-body">
            <div class="form-group"><label>Platform</label><input type="text" class="form-control" value="{{ ucfirst($account->platform) }}" disabled></div>
            <div class="form-group"><label>Access Token</label><input type="password" name="access_token" class="form-control" autocomplete="new-password" placeholder="Leave blank to keep the current token"></div>
            <div class="form-group"><label>Refresh Token</label><input type="password" name="refresh_token" class="form-control" autocomplete="new-password" placeholder="Leave blank to keep the current token"></div>
            <div class="form-group"><label>Display Name</label><input type="text" name="platform_display_name" class="form-control" value="{{ $account->platform_display_name }}"></div>
        </div>
        <div class="card-footer"><button class="btn btn-primary">Update</button> <a href="{{ route('social.accounts.index') }}" class="btn btn-default">Cancel</a></div>
    </form>
</div></div></div>
@endsection
