@extends('layouts.app')
@section('title', 'Too Many Requests')
@section('content')
<div class="error-page">
    <h2 class="headline text-warning">429</h2>
    <div class="error-content">
        <h3><i class="fas fa-tachometer-alt text-warning"></i> Too many requests.</h3>
        <p>You've exceeded your quota. Please upgrade your plan or wait. <a href="{{ route('agency.billing') }}">Upgrade plan</a></p>
    </div>
</div>
@endsection
