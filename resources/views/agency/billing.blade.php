@extends('layouts.app')
@section('title', 'Billing & Plan')
@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Current Plan</h3></div>
            <div class="card-body text-center">
                <h2>{{ ucfirst($agency->subscription_plan) }}</h2>
                <p class="text-muted">{{ $plan['name'] }} Plan</p>
                <h3>${{ number_format($plan['price'], 2) }}<small>/mo</small></h3>
                <ul class="list-unstyled text-sm">
                    <li>{{ $plan['posts_per_month'] == -1 ? 'Unlimited' : $plan['posts_per_month'] }} posts/month</li>
                    <li>{{ $plan['social_accounts'] == -1 ? 'Unlimited' : $plan['social_accounts'] }} social accounts</li>
                    <li>{{ $plan['ai_generations_per_month'] == -1 ? 'Unlimited' : $plan['ai_generations_per_month'] }} AI generations</li>
                    <li>{{ $plan['campaigns'] == -1 ? 'Unlimited' : $plan['campaigns'] }} campaigns</li>
                    <li>{{ $plan['clients'] == -1 ? 'Unlimited' : $plan['clients'] }} clients</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Available Plans</h3></div>
            <div class="card-body">
                <div class="row">
                    @foreach($plans as $key => $p)
                        <div class="col-md-4">
                            <div class="card card-outline {{ $agency->subscription_plan === $key ? 'card-primary' : '' }}">
                                <div class="card-body text-center">
                                    <h5>{{ $p['name'] }}</h5>
                                    <h3>${{ number_format($p['price'], 2) }}<small>/mo</small></h3>
                                    @if($agency->subscription_plan === $key)
                                        <span class="badge badge-success">Current Plan</span>
                                    @elseif($p['price'] > $plan['price'])
                                        <form action="{{ route('agency.billing.upgrade') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="plan" value="{{ $key }}">
                                            <button class="btn btn-sm btn-primary">Upgrade</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
