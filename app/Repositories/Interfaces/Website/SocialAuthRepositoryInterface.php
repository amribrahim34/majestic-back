<?php

namespace App\Repositories\Interfaces\Website;

interface SocialAuthRepositoryInterface
{
    public function findUserBySocialProvider($provider, $socialUser);
    public function findUserByEmail($email);
    public function createUser(array $userData);
    public function updateOrCreateSocialAccount($user, $provider, $socialUser);
    public function createToken($user);
}
