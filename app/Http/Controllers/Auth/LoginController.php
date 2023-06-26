<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use App\Models\UserLoginLog;
use App\Models\User;
use App\Models\UserIpblock;
use App\Models\SiteAdminIplist;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'userid';
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.

        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        $request->request->add(['rolecode' => '0001']); //  add rolecode
        $request->request->add(['isActive' => 1]); //add
        if ($this->attemptLogin($request)) {
            $ipRecords = UserIpblock::all()->toArray();
            if (!empty($ipRecords)) {
                if (array_search($request->ip_addr, array_column($ipRecords, 'ip_addr')) !== false) {
                    Auth::logout();
                    return Redirect::back()
                        ->withErrors(['error' => '관리자에 문의해 주세요[3].']);
                }
            }

            if ($request->hasSession()) {
                $user = Auth::user();
                if ($request->userid != 'elena') {
                    UserLoginLog::insert([
                        'domain' => request()->getHost(),
                        'userid' => $user->id,
                        'ip_addr' => $request->ip_addr
                    ]);
                }
                $request->session()->put('ip_addr', $request->ip_addr);
                $request->session()->put('auth.password_confirmed_at', time());
            }

            return $this->sendLoginResponse($request);
        }
        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }


    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password', 'rolecode', 'isActive');
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt(
            $this->credentials($request), $request->boolean('remember')
        );
    }
}
