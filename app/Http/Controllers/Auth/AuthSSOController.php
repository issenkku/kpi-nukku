<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class AuthSSOController extends Controller
{
    public function redirectToSSO(Request $request)
    {
        $appId = config('sso.app_id');
        $webBaseUrl = rtrim((string) config('sso.web_base_url'), '/');

        if ($appId === '' || $webBaseUrl === '') {
            abort(503, 'SSO is not configured.');
        }

        $state = Str::random(64);
        $request->session()->put('sso_state', $state);

        return redirect($webBaseUrl.'/login?'.http_build_query([
            'app' => $appId,
            'state' => $state,
        ]));
    }

    public function callback(Request $request)
    {
        $expectedState = (string) $request->session()->pull('sso_state', '');
        $receivedState = (string) $request->query('state', '');
        if ($expectedState === '' || $receivedState === '' || ! hash_equals($expectedState, $receivedState)) {
            Log::warning('SSO callback rejected due to invalid state.');

            return redirect('/login')->with('error', 'Invalid SSO state. Please try again.');
        }

        $code = $request->query('code');
        if (! $code) {
            return redirect('/login')->with('error', 'Missing authorization code from SSO.');
        }

        $apiBaseUrl = rtrim((string) config('sso.api_base_url'), '/');
        if ($apiBaseUrl === '') {
            abort(503, 'SSO is not configured.');
        }

        $payload = [
            'code' => $code,
            'redirectUrl' => config('sso.redirect_url'),
            'clientId' => config('sso.client_id'),
            'clientSecret' => config('sso.client_secret'),
        ];
        $response = Http::asJson()
            ->timeout(15)
            ->post($apiBaseUrl.'/auth.token', $payload);

        if ($response->failed()) {
            Log::warning('SSO token exchange failed', [
                'status' => $response->status(),
                'redirectUrl' => $payload['redirectUrl'],
            ]);

            return redirect('/login')->with('error', 'SSO token exchange failed ('.$response->status().').');
        }

        $data = $response->json();

        if (! ($data['ok'] ?? false)) {
            Log::warning('SSO token response was not accepted by the provider.');

            return redirect('/login')->with('error', 'SSO response invalid.');
        }

        $accessToken = $data['accessToken'] ?? null;
        if (! $accessToken) {
            Log::warning('SSO access token missing from provider response.');

            return redirect('/login')->with('error', 'SSO access token missing.');
        }

        $profileRes = Http::withToken($accessToken)
            ->timeout(15)
            ->post($apiBaseUrl.'/user.profile');
        if ($profileRes->failed()) {
            Log::warning('SSO profile fetch failed', [
                'status' => $profileRes->status(),
            ]);

            return redirect('/login')->with('error', 'SSO profile fetch failed ('.$profileRes->status().').');
        }
        $profile = $profileRes->json()['profile'] ?? [];

        // Resolve or create local user, then log in and redirect by role
        $email = $profile['email']
            ?? $profile['mail']
            ?? $profile['kkuMail']
            ?? $profile['primaryEmail']
            ?? null;

        $username = $profile['username']
            ?? $profile['userName']
            ?? $profile['uid']
            ?? $profile['kkuid']
            ?? null;

        $user = null;

        // If no email provided, attempt to find existing user by common email patterns from username
        if (! $email && $username) {
            foreach ([$username.'@kku.ac.th', $username.'@kkumail.com', $username.'@kku.local'] as $guess) {
                $existing = User::where('email', $guess)->first();
                if ($existing) {
                    $user = $existing;
                    $email = $existing->email;
                    break;
                }
            }
        }

        // As last resort, synthesize a local-only email so creation passes DB unique not-null constraint
        if (! $email && $username) {
            $email = $username.'@kku.local';
        }

        if (! $email) {
            Log::warning('SSO profile missing a usable identifier.');

            return redirect('/login')->with('error', 'SSO profile missing email/username.');
        }

        if (! $user) {
            $user = User::where('email', $email)->first();
        }

        if (! $user) {
            // Ensure a valid department exists (users.department_id is not-null)
            $deptName = $profile['facultyName'] ?? ($profile['departmentName'] ?? 'Unassigned');
            $department = Department::firstOrCreate(['name' => $deptName ?: 'Unassigned']);

            $user = User::create([
                'title' => $profile['title'] ?? null,
                'first_name' => $profile['firstname'] ?? ($profile['firstName'] ?? ''),
                'last_name' => $profile['lastname'] ?? ($profile['lastName'] ?? ''),
                'email' => $email,
                'password' => Str::random(32), // hashed by User casts
                'status' => true,
                'department_id' => $department->id,
            ]);
            try {
                if (method_exists($user, 'assignRole')) {
                    $user->assignRole('user');
                }
            } catch (\Throwable $e) {
                Log::warning('Unable to assign default role to new SSO user', ['email' => $email, 'error' => $e->getMessage()]);
            }
        }

        if (! $user->status) {
            Log::notice('Inactive local account rejected during SSO login.', ['user_id' => $user->id]);

            return redirect('/login')->with('error', 'This account has been suspended.');
        }

        Auth::login($user);
        $request->session()->regenerate();

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

        return redirect($redirect)->with('success', 'Login success: '.($profile['firstname'] ?? ''));
    }

    public function logout(Request $request)
    {
        $appId = (string) config('sso.app_id');
        $webBaseUrl = rtrim((string) config('sso.web_base_url'), '/');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($appId === '' || $webBaseUrl === '') {
            return redirect('/login');
        }

        return redirect($webBaseUrl.'/logout?'.http_build_query(['app' => $appId]));
    }
}
