@extends('layouts.app')
@section('title', 'Payment Successful')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1>Payment Successful</h1>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                    <h3>Thank you for your payment!</h3>
                    <p>Your subscription has been activated.</p>
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">Go to Dashboard</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
