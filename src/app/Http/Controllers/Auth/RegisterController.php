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
            $data = $request->only(['name', 'email', 'password']);

            $data['password'] = Hash::make($data['password']);

            $user = User::create($data);

            return ApiResponse::success('USER_REGISTERED', [
                'user' => $user->only(['id', 'name', 'email']),
            ], 201);
        } catch (\Throwable $th) {
            return ApiResponse::error('USER_REGISTRATION_FAILED', [
                'exception' => $th->getMessage(),
            ], 500);
        }
    }
}
