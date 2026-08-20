<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $key = Str::lower($credentials['email']).'|'.$request->ip();
        $maxAttempts = 5;
        $decaySeconds = 60;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            return response()->json([
                'message' => 'พยายามเข้าสู่ระบบมากเกินไป โปรดลองใหม่ภายหลัง.',
            ], 429);
        }

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            RateLimiter::hit($key, $decaySeconds);

            return response()->json([
                'message' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง',
            ], 401);
        }

        if (! $user->status) {
            return response()->json([
                'message' => 'บัญชีผู้ใช้นี้ถูกระงับการใช้งาน',
            ], 403);
        }

        RateLimiter::clear($key);

        Auth::login($user);
        $request->session()->regenerate(); // prevent session fixation

        $redirect = '/dashboard';
        if ($user->hasRole('super_admin')) {
            $redirect = '/dashboard';
        } elseif ($user->hasRole('system_admin')) {
            $redirect = '/dashboard';
        } elseif ($user->hasRole('qa_admin')) {
            $redirect = '/dashboard';
        } elseif ($user->hasRole('administration_admin')) {
            $redirect = '/dashboard';
        } elseif ($user->hasRole('user')) {
            $redirect = '/dashboardkpi';
        }

        return response()->json(['redirect' => $redirect]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'ออกจากระบบสำเร็จ');
    }

    public function user(Request $request)
    {
        // return view('auth.profile', ['user' => $request->user()]);
        return response()->json($request->user());
    }
}
