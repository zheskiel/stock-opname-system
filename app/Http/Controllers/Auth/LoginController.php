<?php

namespace App\Http\Controllers\Auth;

use Auth;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

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
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('guest:manager')->except('logout');
        $this->middleware('guest:admin')->except('logout');
    }

    public function showManagerLoginForm()
    {
        $params = [
            'route'   => route('manager.login-view'),
            'title' => 'manager'
        ];

        return view('auth.login', $params);
    }

    public function managerLogin(Request $request)
    {
        $this->validate($request, [
            'email'   => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::guard('manager')->attempt($request->only(['email','password']), $request->get('remember'))){
            return redirect()->intended('/manager/dashboard');
        }

        return back()->withInput($request->only('email', 'remember'));
    }

    public function showAdminLoginForm()
    {
        $params = [
            'route'   => route('admin.login-view'),
            'title' => 'Admin'
        ];

        return view('auth.login', $params);
    }

    public function AdminLogin(Request $request)
    {
        $this->validate($request, [
            'email'   => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::guard('admin')->attempt($request->only(['email','password']), $request->get('remember'))){
            return redirect()->intended('/admin/dashboard');
        }

        return back()->withInput($request->only('email', 'remember'));
    }
}
