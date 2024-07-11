<?php

namespace App\Repositories\Website;

use App\Models\User;
use App\Repositories\Interfaces\Website\SocialAuthRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SocialAuthRepository implements SocialAuthRepositoryInterface
{
    public function findUserBySocialProvider($provider, $socialUser)
    {
        return User::whereHas('socialAccounts', function ($query) use ($provider, $socialUser) {
            $query->where('provider', $provider)
                ->where('provider_id', $socialUser->getId());
        })->first();
    }

    public function findUserByEmail($email)
    {
        return User::where('email', $email)->first();
    }

    public function createUser(array $userData)
    {
        return User::create([
            'user_name' => $userData['name'],
            'email' => $userData['email'],
            'password' => Hash::make(Str::random(16)),
        ]);
    }

    public function updateOrCreateSocialAccount($user, $provider, $socialUser)
    {
        return $user->socialAccounts()->updateOrCreate(
            [
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
            ],
            [
                'provider_token' => $socialUser->token,
                'provider_refresh_token' => $socialUser->refreshToken,
            ]
        );
    }

    public function createToken($user)
    {
        return $user->createToken('social-auth-token')->plainTextToken;
    }
}
