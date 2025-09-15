<?php

namespace App\Services;

use Google_Client;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GoogleAuthService
{
    public function loginOrRegister(string $idToken): User
    {
        $client = new Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]);

        $payload = $client->verifyIdToken($idToken);

        if (!$payload) {
            throw new \Exception('Invalid Google token');
        }

        $user = User::firstOrCreate(
            ['email' => $payload['email']],
            [
                'name'     => $payload['name'],
                'password' => bcrypt(str()->random(16)),
            ]
        );

        Auth::login($user);

        return $user;
    }
}
