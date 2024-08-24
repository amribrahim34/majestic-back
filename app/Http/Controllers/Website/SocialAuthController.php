<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\SocialAccount;
use App\Repositories\Interfaces\Website\SocialAuthRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    protected $socialAuthRepository;

    public function __construct(SocialAuthRepositoryInterface $socialAuthRepository)
    {
        $this->socialAuthRepository = $socialAuthRepository;
    }

    public function redirect($provider)
    {
        $url = Socialite::driver($provider)->stateless()->redirect()->getTargetUrl();
        return response()->json(['url' => $url]);
    }

    public function callback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid credentials provided.'], 422);
        }

        $user = $this->socialAuthRepository->findUserBySocialProvider($provider, $socialUser);

        if (!$user) {
            $user = $this->socialAuthRepository->findUserByEmail($socialUser->getEmail());

            if (!$user) {
                $user = $this->socialAuthRepository->createUser([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'avatar' => $socialUser->getAvatar(),
                ]);
            }
        }

        $this->socialAuthRepository->updateOrCreateSocialAccount($user, $provider, $socialUser);

        $token = $this->socialAuthRepository->createToken($user);


        $queryParams = http_build_query([
            'token' => $token,
            'user' => json_encode($user)
        ]);

        return redirect(env('FRONTEND_URL') . '/auth-callback?' . $queryParams);
    }
}
