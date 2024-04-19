<?php

namespace App\Contracts\Admin;

interface IAuthService
{
    public function authenticate($email, $password);
}
