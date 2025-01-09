<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\OTPStatus;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OtpController extends Controller
{
    public function __construct(public OtpService $otpService)
    {
    }


    public function show()
    {
        return view('auth.otp');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:4',
        ]);
        $user = Auth::user();

        $status = $this->otpService->validate($user->email, $request->otp);
        if ($status === OTPStatus::VALID) {
            $user->update(['email_verified_at' => now()]);
            return redirect()->route('dashboard');
        }
        return view('auth.otp')->withErrors(['otp' => $status]);
    }

    public function resend()
    {
        $email = Auth::user()->email;
        try {
            $this->ensureIsNotRateLimited($email);
        } catch (ValidationException $e) {
            return view('auth.otp')->withErrors(['otp' => $e->getMessage()]);
        }

        $otp = $this->otpService->generate($email, validity: 1);
        RateLimiter::hit($this->throttleKey($email));

        Mail::to($email)->send(new OtpMail($otp));
        return view('auth.otp')->with('status', trans('OTP sent to your email!'));
    }

    public function ensureIsNotRateLimited(string $email): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey($email), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($email));
        throw ValidationException::withMessages([
            'otp' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey($email): string
    {
        return Str::transliterate(Str::lower($email));
    }

}
