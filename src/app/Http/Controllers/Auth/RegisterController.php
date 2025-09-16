<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Support\Facades\Hash;
use App\Support\ApiResponse;
use App\Notifications\VerifyEmailApi;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try {
            $data = $request->only(['name', 'email', 'password']);
            $data['password'] = Hash::make($data['password']);

            $user = User::create($data);

            try {
                $user->notify(new VerifyEmailApi());
            } catch (\Throwable $th) {
                return ApiResponse::error('EMAIL_VERIFICATION_FAILED', [
                    'exception' => $th->getMessage()
                ], 500);
            }

            $token = $user->createToken('api-token')->plainTextToken;

            return ApiResponse::success('USER_REGISTERED', [
                'user' => $user->only(['id', 'name', 'email']),
                'token' => $token,
                'message' => 'Please verify your email before logging in.'
            ], 201);

        } catch (\Throwable $th) {
            return ApiResponse::error('USER_REGISTRATION_FAILED', [
                'exception' => $th->getMessage(),
            ], 500);
        }
    }
}
