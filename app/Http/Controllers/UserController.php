<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Services\UserService;

class UserController extends Controller
{
    public function store(
        StoreUserRequest $request,
        UserService $userService
    ) {
        $userService->create($request->validated());

        return redirect('/users/create')->with('success', 'User created successfully.');
    }

    public function create()
    {
        return view('auth.create-user');
    }
}
