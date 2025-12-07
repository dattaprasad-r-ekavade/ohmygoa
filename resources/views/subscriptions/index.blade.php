@extends('layouts.public')

@section('title', 'Pricing Plans')

@section('content')
    <!--PRICING DETAILS-->
    <section class="pri">
        <div class="container">
            <div class="row">
                <div class="tit">
                    <h2>
                        Choose your
                        <span>Pricing Plan</span>
                    </h2>
                    <p>Select the perfect plan for your business needs. Upgrade anytime as you grow.</p>
                </div>
                <div>
                    <ul>
                        @foreach($plans as $key => $plan)
                            <li>
                                <div class="pri-box">
                                    <div class="c2">
                                        <h4>{{ $plan['name'] }} plan</h4>
                                        @if(isset($currentSubscription) && $currentSubscription->plan_id == $key)
                                            <span class="badge badge-success">Current Plan</span>
                                        @else
                                            <p>Great for your business</p>
                                        @endif
                                    </div>
                                    <div class="c3">
                                        <h2>
                                            <span>$</span>{{ $plan['monthly'] }}
                                        </h2>
                                        <p>Per Month</p>
                                        @if(auth()->check())
                                            @if(isset($currentSubscription) && $currentSubscription->plan_id == $key)
                                                <a href="#" class="btn btn-secondary disabled">Current</a>
                                            @else
                                                <a href="#">Select Plan</a>
                                            @endif
                                        @else
                                            <a href="{{ route('login') }}">Add listing</a>
                                        @endif
                                    </div>
                                    <div class="c4">
                                        <ol>
                                            @foreach($plan['features'] as $feature)
                                                <li>{{ $feature }}</li>
                                            @endforeach
                                        </ol>
                                    </div>
                                    <div class="c5">
                                        @if(auth()->check())
                                            <a href="#">Get Started</a>
                                        @else
                                            <a href="{{ route('login') }}">Get Started</a>
                                        @endif
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!--END PRICING DETAILS-->
@endsection
