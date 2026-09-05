@extends('layouts.app')
@section('title', 'Page Expired')
@section('content')
<div class="error-page">
    <h2 class="headline text-warning">419</h2>
    <div class="error-content">
        <h3><i class="fas fa-clock text-warning"></i> Page expired.</h3>
        <p>Your session has expired. Please refresh and try again. <a href="{{ route('dashboard') }}">Return to dashboard</a></p>
    </div>
</div>
@endsection
