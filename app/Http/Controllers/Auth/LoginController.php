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
    protected function authenticate(Request $request)
    {
        $currentEventId = getCurrentEvent();
        $credentials = $request->only('email', 'password');
        $emailOrMobile = $request->input('email');
        $otp = $request->input('otp');
        $loginType = $request->login_type ?? 'visitor';

        if ($loginType === 'exhibitor') {
            $exhibitorRequestedOtp = Exhibitor::where(function ($query) use ($emailOrMobile) {
                $query->where('mobile_number', $emailOrMobile)
                    ->orWhereHas('contact_persons', function ($query) use ($emailOrMobile) {
                        $query->where('contact_number', $emailOrMobile);
                    });
            })
                ->where('otp', $otp)
                ->where('otp_expired_at', '>', now())
                ->first();

            $loginWithExhibitorContactPerson = Exhibitor::where(function ($query) use ($emailOrMobile) {
                $query->where('mobile_number', $emailOrMobile)
                    ->orWhereHas('exhibitorContact', function ($query) use ($emailOrMobile) {
                        $query->where('contact_number', $emailOrMobile);
                    });
            })->first();

            $canLoginExhibitorContactPerson = false;
            if ($loginWithExhibitorContactPerson) {
                $canLoginExhibitorContactPerson = Hash::check($credentials['password'], $loginWithExhibitorContactPerson->password);
            }

            if (
                Auth::guard('exhibitor')->attempt([
                    'mobile_number' => $emailOrMobile,
                    'password' => $credentials['password'],
                ]) || $exhibitorRequestedOtp || $canLoginExhibitorContactPerson
            ) {

                if ($exhibitorRequestedOtp || $canLoginExhibitorContactPerson) {
                    Auth::guard('exhibitor')->login($canLoginExhibitorContactPerson ? $loginWithExhibitorContactPerson : $exhibitorRequestedOtp);
                }

                $user = auth()->guard('exhibitor')->user();

                UserLoginActivity::create([
                    'userable_id' => $user->id,
                    'userable_type' => Exhibitor::class,
                    'last_login_at' => now(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                $isExhibitorRegistered = EventExhibitor::where('event_id', $currentEventId->id)
                    ->where('exhibitor_id', $user->id)
                    ->exists();

                if ($isExhibitorRegistered) {
                    return redirect()->intended('/event_information?eventId=' . $currentEventId->id);
                } else {
                    return redirect()->intended('/dashboard');
                }
            } else {
                return redirect()->route('login')
                    ->with(['mobile_no' => $emailOrMobile, 'login_type' => $loginType, 'requested_otp' => !empty($otp) ? 'yes' : 'no', 'show_sign_in_button' => true])
                    ->with('error', 'Invalid credentials for Exhibitor login.');
            }
        } elseif ($loginType === 'visitor') {
            $validatedVisitorOtp = Visitor::where('mobile_number', $emailOrMobile)
                ->where('otp', $otp)
                ->where('otp_expired_at', '>', now())
                ->first();

            if (Auth::guard('visitor')->attempt(['mobile_number' => $emailOrMobile, 'password' => $credentials['password']]) || $validatedVisitorOtp) {
                if ($validatedVisitorOtp) {
                    $validatedVisitorOtp->otp = null;
                    $validatedVisitorOtp->otp_expired_at = null;
                    $validatedVisitorOtp->save();

                    Auth::guard('visitor')->login($validatedVisitorOtp);
                }

                UserLoginActivity::create([
                    'userable_id' => auth()->guard('visitor')->user()->id,
                    'userable_type' => Visitor::class,
                    'last_login_at' => now(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                $isVisitorRegistered = EventVisitor::where('event_id', $currentEventId->id)
                    ->where('visitor_id', getAuthData()->id)
                    ->exists();
                if ($isVisitorRegistered) {
                    return redirect()->intended('/event_information?eventId=' . $currentEventId->id);
                } else {
                    return redirect()->intended('/dashboard');
                }
            } else {
                return redirect()->route('login')
                    ->with(['mobile_no' => $emailOrMobile, 'login_type' => $loginType, 'requested_otp' => !empty($otp) ? 'yes' : 'no', 'show_sign_in_button' => true])
                    ->with('error', 'Invalid credentials for Visitor login.');
            }
        } else {

            return redirect()->route('login')
                ->with(['mobile_no' => $emailOrMobile, 'login_type' => $loginType])
                ->with('error', 'Invalid login type.');
        }
    }

    public function requestOtp(Request $request)
    {
        $loginType = $request->input('otp_login_type');
        $mobileNo = $request->input('mobile_number');

        if ($loginType == 'visitor') {
            $userData = Visitor::where('mobile_number', $mobileNo)->first();
        } else if ($loginType == 'exhibitor') {
            $userData = Exhibitor::where('mobile_number', $mobileNo)
                ->orWhereHas('contact_persons', function ($query) use ($mobileNo) {
                    $query->where('contact_number', $mobileNo);
                })->first();
        }

        if (!$userData) {
            return redirect()->back()
                ->with('mobile_no', $mobileNo)
                ->with('login_type', $loginType)
                ->with('requested_otp', 'yes')
                ->with('error', 'Mobile number not found')
                ->with('show_sign_in_button', false);
        }

        $otp = rand(100000, 999999);
        $sendOtp = sendLoginOtp($mobileNo, $otp, $loginType);
        if ($sendOtp['status'] === 'success') {

            $userData->otp = $otp;
            $userData->otp_expired_at = now()->addMinutes(10);
            $userData->save();

            if ($userData) {
                return redirect()->back()
                    ->with('mobile_no', $mobileNo)
                    ->with('requested_otp', 'yes')
                    ->with('login_type', $loginType)
                    ->with('success', 'OTP sent successfully, It will expire in 10 minutes.')
                    ->with('show_sign_in_button', true);
            }
        }
        return redirect()->back()
            ->with('mobile_no', $mobileNo)
            ->with('requested_otp', 'yes')
            ->with('login_type', $loginType)
            ->with('error', 'Something went wrong, try again later')
            ->with('show_sign_in_button', false);
    }

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
