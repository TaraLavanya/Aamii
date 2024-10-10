<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Visitor;
use App\Models\Exhibitor;
use App\Models\EventVisitor;
use Illuminate\Http\Request;
use App\Models\EventExhibitor;
use App\Models\UserLoginActivity;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController as FortifyAuthenticatedSessionController;

class LoginController extends FortifyAuthenticatedSessionController
{
    protected function authenticateAdmin(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $verifiedUser = false;
        // Attempt to authenticate using  mobile number
        // if (Auth::guard('web')->attempt(['mobile_number' => $credentials['email'], 'password' => $credentials['password']])) {
        //     $verifiedUser = true;
        // }

        // Attempt to authenticate using email  
        if (Auth::guard('web')->attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
            $verifiedUser = true;
        }
        if ($verifiedUser) {
            // dd($verifiedUser);
            UserLoginActivity::create([
                'userable_id' => auth()->guard('web')->user()->id,
                'userable_type' => User::class,
                'last_login_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            return redirect()->intended('/dashboard');
        }
        return redirect()->route('admin-login-form')->with('mobile_no', $credentials['email'])->with('error', 'Invalid credentials');
    }

    public function logout(Request $request)
    {
        Log::info('Logout');

        if (Auth::guard('exhibitor')->check()) {
            $lastUserLoginActivity = UserLoginActivity::where('userable_id', auth()->guard('exhibitor')->user()->id)
                ->where('userable_type', 'App\Models\Exhibitor')
                ->where('last_logout_at', null)
                ->orderBy('id', 'desc')
                ->first();

            if ($lastUserLoginActivity) {
                $lastUserLoginActivity->last_logout_at = now();
                $lastUserLoginActivity->save();
            }
        } elseif (Auth::guard('visitor')->check()) {
            $lastUserLoginActivity = UserLoginActivity::where('userable_id', auth()->guard('visitor')->user()->id)
                ->where('userable_type', 'App\Models\Visitor')
                ->where('last_logout_at', null)
                ->orderBy('id', 'desc')
                ->first();

            if ($lastUserLoginActivity) {
                $lastUserLoginActivity->last_logout_at = now();
                $lastUserLoginActivity->save();
            }
        }

        Auth::guard('exhibitor')->logout();
        Auth::guard('visitor')->logout();

        $request->session()->flush();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
