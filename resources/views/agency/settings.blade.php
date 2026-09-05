@extends('layouts.app')
@section('title', 'Agency Settings')
@section('content')
<div class="row"><div class="col-md-8"><div class="card"><div class="card-header"><h3 class="card-title"><i class="fas fa-cog mr-2"></i>Agency Settings</h3></div>
    <form action="{{ route('agency.settings.update') }}" method="PUT">@csrf
        <div class="card-body">
            <div class="form-group"><label>Agency Name</label><input type="text" name="name" class="form-control" value="{{ $agency->name }}" required></div>
            <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" value="{{ $agency->email }}" required></div>
            <div class="form-group"><label>Website</label><input type="url" name="website" class="form-control" value="{{ $agency->website }}"></div>
            <div class="form-group"><label>Phone</label><input type="text" name="phone" class="form-control" value="{{ $agency->phone }}"></div>
            <div class="form-group"><label>Address</label><input type="text" name="address" class="form-control" value="{{ $agency->address }}"></div>
            <div class="form-group"><label>Timezone</label><input type="text" name="timezone" class="form-control" value="{{ $agency->timezone }}"></div>
            <div class="form-group"><label>Currency</label><input type="text" name="currency" class="form-control" value="{{ $agency->currency }}"></div>
            <div class="form-group"><label>Description</label><textarea name="description" class="form-control" rows="3">{{ $agency->description }}</textarea></div>
        </div>
        <div class="card-footer"><button class="btn btn-primary">Save Changes</button></div>
    </form>
</div></div></div>
@endsection
