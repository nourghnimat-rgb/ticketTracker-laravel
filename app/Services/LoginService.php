<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class LoginService
{
    public function attempt(array $credentials): bool
    {
        return Auth::attempt($credentials);
    }
    public function logout(): void
{
    Auth::logout();
}
}