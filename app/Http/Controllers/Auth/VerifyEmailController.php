<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->is_verified) {
            return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
        }

        $request->user()->update([
            'is_verified' => true,
            'verified_at' => now(),
            'email_verified_at' => now(),
        ]);

        event(new Verified($request->user()));

        return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
    }
}
