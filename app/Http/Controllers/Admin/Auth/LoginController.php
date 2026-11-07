<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SendNewPassword;
use App\Models\ActivityLog;
use App\Models\Admin;
use App\Providers\RouteServiceProvider;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    protected $redirectTo = RouteServiceProvider::ADMIN;

    public function __construct()
    {
        $this->middleware('guest:admin')->except('logout');
    }

    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function username()
    {
        return 'email';
    }

    public function showForgetForm()
    {
        return view('admin.auth.forget');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = Admin::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No user found with this email address.']);
        }

        $newPassword = Str::random(10);
        $user->password = Hash::make($newPassword);
        $user->save();

        try {
            Mail::to($user->email)->send(new SendNewPassword($user, $newPassword));
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Failed to send email. Please try again later.']);
        }

        Toastr::success('A new password has been sent to your email.', 'Success');

        return redirect()->route('admin.login');
    }


    // ------------------------------------------------------
    // FORTIFY MANUAL LOGIN IMPLEMENTATION (REPLACING TRAIT)
    // ------------------------------------------------------
    public function login(Request $request)
    {
        $this->validateLogin($request);

        $credentials = $request->only($this->username(), 'password');
        $user = Admin::where($this->username(), $request->email)->first();

        if (!$user) {
            return $this->sendFailedLoginResponse($request, 'User not found');
        }

        if ($user->status == '0') {
            return $this->sendFailedLoginResponse($request, 'inactive');
        }

        if (Auth::guard('admin')->attempt($credentials, $request->filled('remember'))) {

            ActivityLog::log(
                category: ActivityLog::CATEGORY_AUTH,
                event: 'admin_login',
                status: 'success',
                message: 'Admin logged in: ' . $user->email,
                userId: $user->id,
                userEmail: $user->email,
                userName: $user->name ?? 'Admin',
            );

            Artisan::call('route:clear');
            Artisan::call('optimize:clear');
            Artisan::call('view:clear');
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            Artisan::call('clear-compiled');
            Artisan::call('storage:link');
            Artisan::call('cache:forget spatie.permission.cache');

            return redirect()->intended($this->redirectTo);
        }

        return $this->sendFailedLoginResponse($request, 'invalid');
    }


    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    protected function sendFailedLoginResponse(Request $request, $error)
    {
        $message = $error === 'inactive'
            ? 'Your account is inactive. Please contact support team.'
            : 'These credentials do not match our records.';


        return back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors([$this->username() => $message]);
    }

    protected function guard()
    {
        return Auth::guard('admin');
    }

    public function logout(Request $request)
    {
        $admin = $this->guard()->user();
        if ($admin) {
            ActivityLog::log(
                category: ActivityLog::CATEGORY_AUTH,
                event: 'admin_logout',
                status: 'info',
                message: 'Admin logged out: ' . $admin->email,
                userId: $admin->id,
                userEmail: $admin->email,
                userName: $admin->name ?? 'Admin',
            );
        }

        $this->guard()->logout();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}

