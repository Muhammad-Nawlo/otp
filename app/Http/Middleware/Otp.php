<?php

namespace App\Http\Middleware;

use App\Services\OtpService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Otp
{
    public function __construct(protected OtpService $otpService)
    {
    }
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Check if OTP is still valid
        if (!$user->otp || $user->otp_expires_at < now()) {
            // Resend OTP if expired or missing
            try {
                $this->otpService->generate($user);
            } catch (\Exception $e) {
                return redirect()->route('login')->withErrors(['status' => $e->getMessage()]);
            }

            return redirect()->route('otp.form')->with('status', 'Your OTP has expired. A new OTP has been sent to your email.');
        }

        return $next($request);
    }
}
