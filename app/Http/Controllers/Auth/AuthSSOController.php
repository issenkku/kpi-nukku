<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class AuthSSOController extends Controller
{
    public function redirectToSSO()
    {
        $appId = config('sso.app_id');
        // dd($appId);
        return redirect("https://sso-uat-web.kku.ac.th/login?app={$appId}");
    }

    public function callback(Request $request)
    {
        $code = $request->query('code');
        // echo $code; exit;
        if (!$code) {
            return redirect('/login')->with('error', 'Missing authorization code from SSO.');
        }

        $payload = [
            'code' => $code,
            'redirectUrl' => config('sso.redirect_url'),
            'clientId' => config('sso.client_id'),
            'clientSecret' => config('sso.client_secret'),
        ];
        $response = Http::withHeaders(['Content-Type' => 'application/json',])->post('https://sso-uat-api.kku.ac.th/auth.token', $payload);

        if ($response->failed()) {
            Log::warning('SSO token exchange failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'redirectUrl' => $payload['redirectUrl'],
            ]);
            return redirect('/login')->with('error', 'SSO token exchange failed (' . $response->status() . ').');
        }

        $data = $response->json();
     
        if (!($data['ok'] ?? false)) {
            Log::warning('SSO token response not ok', ['data' => $data]);
            return redirect('/login')->with('error', 'SSO response invalid.');
        }

        $accessToken = $data['accessToken'] ?? null;
        if (!$accessToken) {
            Log::warning('SSO accessToken missing', ['data' => $data]);
            return redirect('/login')->with('error', 'SSO access token missing.');
        }

        $profileRes = Http::withToken($accessToken)
            ->post('https://sso-uat-api.kku.ac.th/user.profile');
        if ($profileRes->failed()) {
            Log::warning('SSO profile fetch failed', [
                'status' => $profileRes->status(),
                'body' => $profileRes->body(),
            ]);
            return redirect('/login')->with('error', 'SSO profile fetch failed (' . $profileRes->status() . ').');
        }
        $profile = $profileRes->json()['profile'] ?? [];
        Log::info('SSO profile payload', ['profile' => $profile]);

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
        if (!$email && $username) {
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
        if (!$email && $username) {
            $email = $username.'@kku.local';
        }

        if (!$email) {
            Log::warning('SSO profile missing usable identifier', ['profile' => $profile]);
            return redirect('/login')->with('error', 'SSO profile missing email/username.');
        }

        if (!$user) {
            $user = User::where('email', $email)->first();
        }

        if (!$user) {
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

        return redirect($redirect)->with('success', 'Login success: ' . ($profile['firstname'] ?? ''));
    }

    public function logout()
    {
        $appId = config('sso.app_id');
        return redirect("https://sso-uat-web.kku.ac.th/logout?app={$appId}");
    }
}
