<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Services\LoginService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function show()
    {

        return view('auth.login');
    }
    public function login(LoginRequest $request, LoginService $loginService)
    {
        if (! $loginService->attempt($request->validated())) {
            return back()->withErrors(['email' => 'Invalid email or password.']);
        }
        $request->session()->regenerate();

        return redirect('/');
    }
    public function logout(Request $request, LoginService $loginService)
    {
        $loginService->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
