<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Support\Facades\Hash;
use App\Support\ApiResponse;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return ApiResponse::success('USER_REGISTERED', [
                'user' => $user,
            ], 201);
        } catch (\Throwable $th) {
            return ApiResponse::error('USER_REGISTRATION_FAILED', [
                'exception' => $th->getMessage(),
            ], 500);
        }
    }
}