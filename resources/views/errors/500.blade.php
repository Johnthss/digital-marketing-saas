@extends('layouts.app')
@section('title', 'Server Error')
@section('content')
<div class="error-page">
    <h2 class="headline text-danger">500</h2>
    <div class="error-content">
        <h3><i class="fas fa-times-circle text-danger"></i> Something went wrong.</h3>
        <p>We're experiencing a technical issue. Please try again later. <a href="{{ route('dashboard') }}">Return to dashboard</a></p>
    </div>
</div>
@endsection
