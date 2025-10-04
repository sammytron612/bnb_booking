<?php

namespace App\Http\Controllers;

use App\Mail\OtpMail;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Carbon\Carbon;

class OtpController extends Controller
{
    /**
     * Show the OTP verification form
     */
    public function show()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Check if user already has OTP verified in session
        if (session('otp_verified')) {
            return redirect()->intended('/danya-admin');
        }

        // Generate and send OTP if not already sent recently
        $user = Auth::user();
        $recentOtp = Otp::where('user_id', $user->id)
            ->where('used', false)
            ->where('expires_at', '>', Carbon::now())
            ->latest()
            ->first();

        if (!$recentOtp) {
            $this->generateAndSendOtp($user);
        }

        return view('auth.otp-verify', [
            'email' => $user->email,
            'canResend' => !$recentOtp || Carbon::now()->diffInSeconds($recentOtp->created_at) >= 30
        ]);
    }

    /**
     * Verify the OTP code
     */
    public function verify(Request $request)
    {
        $request->validate([
            'otp_code' => 'required|string|size:6'
        ]);

        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $otpCode = $request->otp_code;

        // Rate limiting for OTP verification attempts
        $key = 'otp-verify:' . $user->id;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'otp_code' => "Too many verification attempts. Please try again in {$seconds} seconds."
            ]);
        }

        RateLimiter::hit($key, 300); // 5 minutes lockout

        // Find valid OTP
        $otp = Otp::where('user_id', $user->id)
            ->where('code', $otpCode)
            ->where('used', false)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$otp) {
            return back()->withErrors([
                'otp_code' => 'Invalid or expired OTP code. Please request a new one.'
            ]);
        }

        // Mark OTP as used
        $otp->update([
            'used' => true,
            'verified_at' => Carbon::now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        // Set session verification
        session(['otp_verified' => true]);

        // Clear rate limiting on successful verification
        RateLimiter::clear($key);

        return redirect()->intended('/danya-admin')->with('success', 'OTP verified successfully!');
    }

    /**
     * Resend OTP code
     */
    public function resend(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Rate limiting for OTP resend
        $key = 'otp-resend:' . $user->id;
        if (RateLimiter::tooManyAttempts($key, 3)) {
            return back()->withErrors([
                'resend' => 'Too many resend attempts. Please wait before requesting another code.'
            ]);
        }

        RateLimiter::hit($key, 300); // 5 minutes lockout

        // Mark previous OTPs as used
        Otp::where('user_id', $user->id)
            ->where('used', false)
            ->update(['used' => true]);

        // Generate and send new OTP
        $this->generateAndSendOtp($user);

        return back()->with('success', 'A new OTP code has been sent to your email.');
    }

    /**
     * Generate and send OTP to user
     */
    private function generateAndSendOtp(User $user)
    {
        // Generate 6-digit OTP
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Create OTP record
        Otp::create([
            'user_id' => $user->id,
            'code' => $otpCode,
            'expires_at' => Carbon::now()->addMinutes(10),
            'used' => false,
        ]);

        // Send email
        try {
            Mail::to($user->email)->send(new OtpMail($otpCode, $user->name));
        } catch (\Exception $e) {
            \Log::error('Failed to send OTP email: ' . $e->getMessage());
            // Could show error to user, but for security, we'll just log it
        }
    }
}
