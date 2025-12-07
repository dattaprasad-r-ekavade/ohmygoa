@extends('layouts.dashboard')

@section('title', 'Add Service Expert')

@section('content')
<div class="dashboard-page">
    <div class="dash-hea mb-4">
        <h1>Publish Your Service Expert Profile</h1>
        <p>Highlight your expertise, showcase certifications, and start receiving leads from the Ohmygoa community.</p>
    </div>

    @include('service-experts.partials.form', [
        'expert' => null,
        'categories' => $categories,
        'locations' => $locations,
        'formAction' => route('business.service-experts.store'),
        'method' => 'POST',
        'submitLabel' => 'Create Profile',
    ])
</div>
@endsection
