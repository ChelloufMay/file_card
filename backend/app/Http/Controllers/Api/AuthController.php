<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\VerificationCode;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Log;

use App\Services\EmailService;
use Throwable;

class AuthController extends Controller
{
    protected EmailService $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    // Register (unchanged behavior: create user and return token)
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'phone_carrier' => $data['phone_carrier'] ?? null,
        ]);

        // Optionally fire Registered event:
        event(new Registered($user));

        $authToken = $user->createToken('notter-token')->plainTextToken;

        return response()->json(['user' => $user, 'token' => $authToken], 201);
    }

    /**
     * LOGIN: validate credentials.
     * If email already verified -> return token.
     * Else -> create verification code and send it by email and return verification_token.
     *
     * Request body: { email, password }
     */
    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        $user = User::query()->where('email', $data['email'])->first();

        if (!$user || ! Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Always require email verification code on login for security
        // (Even if email was previously verified, we still require a code on each login)
        
        // create verification code row and send via email
        try {
            $code = (string) random_int(100000, 999999); // 6-digit safer
        } catch (Throwable $e) {
            Log::warning('random_int failed, falling back to mt_rand: ' . $e->getMessage());
            $code = (string) mt_rand(100000, 999999);
        }

        $verificationToken = hash('sha256', Str::random(40) . now()->timestamp);

        $verification = VerificationCode::query()->create([
            'user_id' => $user->id,
            'contact' => $user->email,
            'code' => $code,
            'method' => 'email',
            'purpose' => 'login',
            'token' => $verificationToken,
            'expires_at' => now()->addMinutes(10),
            'used' => false,
        ]);

        // send email via PHPMailer wrapper (EmailService) - wrapped in try/catch
        $subject = 'Your Notter verification code';
        try {
            $sent = $this->emailService->sendView(
                $user->email,
                'emails.verification_code',
                ['code' => $code, 'expires' => $verification->expires_at, 'subject' => $subject],
                $subject
            );
        } catch (Throwable $e) {
            Log::error('EmailService threw an exception: ' . $e->getMessage(), ['user_id' => $user->id]);
            $sent = false;
        }

        if (! $sent) {
            // mark verification used to avoid reuse (optional)
            $verification->used = true;
            $verification->save();

            Log::error('Failed to send verification email', ['user_id' => $user->id, 'email' => $user->email]);
            return response()->json(['message' => 'Failed to send verification email. Please try again later.'], 500);
        }

        return response()->json([
            'message' => 'verification_required',
            'verification_token' => $verificationToken,
            'method' => 'email',
            'expires_in_minutes' => 10,
        ], 202);
    }

    /**
     * Verify the code from login flow and then issue auth token.
     * Request: { verification_token, code }
     */
    public function verifyLogin(Request $request)
    {
        $request->validate([
            'verification_token' => 'required|string',
            'code' => 'required|string',
        ]);

        $token = $request->input('verification_token');
        $code = $request->input('code');

        $v = VerificationCode::query()
            ->where('token', $token)
            ->where('used', false)
            ->where('expires_at', '>=', now())
            ->where('purpose', 'login')
            ->first();

        if (!$v || $v->code !== $code) {
            return response()->json(['message' => 'Invalid or expired verification code'], 422);
        }

        // mark used
        $v->used = true;
        $v->save();

        // mark user's verification (email)
        // use explicit relation query to help static analyzers
        $user = $v->user()->first();
        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->email_verified_at = now();
        $user->save();

        // Issue token now (separate variable name to avoid confusion)
        $authToken = $user->createToken('notter-token')->plainTextToken;

        return response()->json(['user' => $user, 'token' => $authToken]);
    }

    // logout unchanged
    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $request->user()->currentAccessToken()?->delete();
        }
        return response()->json(['message' => 'Logged out']);
    }

    // optional me endpoint
    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
