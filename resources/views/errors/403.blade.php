@extends('layouts.app')
@section('title', 'Access Denied')
@section('content')
<div class="error-page">
    <h2 class="headline text-danger">403</h2>
    <div class="error-content">
        <h3><i class="fas fa-ban text-danger"></i> Access denied.</h3>
        <p>You don't have permission to access this page. <a href="{{ route('dashboard') }}">Return to dashboard</a></p>
    </div>
</div>
@endsection
