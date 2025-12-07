@extends('layouts.dashboard')

@section('title', 'Edit Service Expert')

@section('content')
<div class="dashboard-page">
    <div class="dash-hea mb-4">
        <h1>Edit Service Expert Profile</h1>
        <p>Keep your service information accurate so customers can make fast, confident decisions.</p>
    </div>

    @include('service-experts.partials.form', [
        'expert' => $expert,
        'categories' => $categories,
        'locations' => $locations,
        'formAction' => route('business.service-experts.update', $expert),
        'method' => 'PATCH',
        'submitLabel' => 'Save Changes',
    ])
</div>
@endsection
