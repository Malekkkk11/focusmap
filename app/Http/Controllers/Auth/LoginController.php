<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        return redirect()->intended($this->redirectTo)->with('success', 'Welcome back, ' . $user->name . '!');
    }

    protected function loggedOut(Request $request)
    {
        return redirect()->route('login')->with('success', 'You have been successfully logged out.');
    }
}