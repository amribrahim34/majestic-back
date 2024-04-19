<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Admin\IAuthService;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminLoginRequest;
use Illuminate\Http\Response;


class AdminAuthController extends Controller
{

    private $authService;

    public function __construct(IAuthService $authService)
    {
        $this->authService = $authService;
    }



    public function login(AdminLoginRequest $request)
    {
        $admin = $this->authService->authenticate($request->email, $request->password);

        if (!$admin) {
            return response()->json(['message' => 'Authentication failed.'], 403);
        }

        $token = $admin->createToken('adminToken')->plainTextToken;

        return response()->json([
            'admin' => $admin->makeHidden(['password']),
            'token' => $token,
        ], Response::HTTP_OK); // Use HTTP status codes from the Response facade
    }
}
