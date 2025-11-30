@extends('emails.layout')

@section('title', 'Verify Your Email Address')

@section('content')
    <h2>Verify Your Email Address</h2>
    
    <p>Hi {{ $user->name }},</p>
    
    <p>Thank you for registering with Ohmygoa! To complete your registration and activate your account, please verify your email address by clicking the button below.</p>
    
    <center>
        <a href="{{ $verificationUrl }}" class="btn">
            Verify Email Address
        </a>
    </center>
    
    <div class="info-box" style="margin-top: 30px;">
        <p style="margin: 0; font-size: 14px;"><strong>Having trouble clicking the button?</strong></p>
        <p style="margin: 5px 0 0 0; font-size: 13px; color: #666; word-break: break-all;">
            Copy and paste this URL into your browser:<br>
            <a href="{{ $verificationUrl }}" style="color: #667eea;">{{ $verificationUrl }}</a>
        </p>
    </div>
    
    <p style="margin-top: 25px; color: #999999; font-size: 14px;">
        <strong>Important:</strong> This verification link will expire in 24 hours for security reasons.
    </p>
    
    <p style="color: #999999; font-size: 14px;">
        If you didn't create an account with Ohmygoa, please ignore this email or contact our support team if you have concerns.
    </p>
@endsection
