<?php

namespace App\Services\Admin;

use App\Contracts\Admin\IAuthService;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminAuthService implements IAuthService
{
    public function authenticate($email, $password)
    {
        $admin = Admin::where('email', $email)->first();

        if (!$admin || !Hash::check($password, $admin->password)) {
            return null; // Return null if authentication fails
        }

        return $admin;
    }
}
