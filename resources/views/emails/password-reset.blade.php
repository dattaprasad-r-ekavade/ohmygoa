@extends('emails.layout')

@section('title', 'Reset Your Password')

@section('content')
    <h2>Password Reset Request</h2>
    
    <p>Hi {{ $user->name }},</p>
    
    <p>We received a request to reset the password for your Ohmygoa account. If you made this request, click the button below to set a new password:</p>
    
    <center>
        <a href="{{ $resetUrl }}" class="btn">
            Reset Password
        </a>
    </center>
    
    <div class="info-box" style="margin-top: 30px;">
        <p style="margin: 0; font-size: 14px;"><strong>Having trouble clicking the button?</strong></p>
        <p style="margin: 5px 0 0 0; font-size: 13px; color: #666; word-break: break-all;">
            Copy and paste this URL into your browser:<br>
            <a href="{{ $resetUrl }}" style="color: #667eea;">{{ $resetUrl }}</a>
        </p>
    </div>
    
    <p style="margin-top: 25px; color: #999999; font-size: 14px;">
        <strong>Security Notice:</strong> This password reset link will expire in 60 minutes.
    </p>
    
    <p style="color: #999999; font-size: 14px;">
        <strong>Didn't request a password reset?</strong><br>
        If you didn't request this password reset, please ignore this email. Your password will remain unchanged. However, if you're concerned about your account security, please contact our support team immediately.
    </p>
    
    <p style="color: #999999; font-size: 14px;">
        For your security, never share your password reset link with anyone.
    </p>
@endsection
