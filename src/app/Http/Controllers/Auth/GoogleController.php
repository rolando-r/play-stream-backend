<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\GoogleAuthService;
use App\Support\ApiResponse;

class GoogleController extends Controller
{
    protected $googleService;

    public function __construct(GoogleAuthService $googleService)
    {
        $this->googleService = $googleService;
    }

    public function login(Request $request)
    {
        try {
            $user = $this->googleService->loginOrRegister($request->id_token);

            return ApiResponse::success('USER_LOGGED_IN_WITH_GOOGLE', [
                'user' => $user->only(['id', 'name', 'email']),
            ]);
        } catch (\Throwable $th) {
            return ApiResponse::error('GOOGLE_AUTH_FAILED', [
                'exception' => $th->getMessage(),
            ], 401);
        }
    }
}
