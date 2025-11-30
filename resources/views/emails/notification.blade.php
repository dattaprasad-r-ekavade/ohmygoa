@extends('emails.layout')

@section('title', $title)

@section('content')
    <h2>{{ $title }}</h2>
    
    <p>Hi {{ $user->name }},</p>
    
    <div style="margin: 25px 0;">
        {!! nl2br(e($message)) !!}
    </div>
    
    @if($actionUrl && $actionText)
        <center>
            <a href="{{ $actionUrl }}" class="btn">
                {{ $actionText }}
            </a>
        </center>
    @endif
    
    <p style="margin-top: 30px; color: #999999; font-size: 14px;">
        This is an automated notification from Ohmygoa. If you have any questions, please contact our support team.
    </p>
@endsection
