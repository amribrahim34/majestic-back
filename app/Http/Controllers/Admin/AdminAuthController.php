<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if (Auth::guard('admin')->attempt($credentials)) {
            $admin = Auth::guard('admin')->user();
            $token = $admin->createToken('adminToken', ['*'])->plainTextToken;


            return response()->json([
                'admin' => $admin,
                'token' => $token,
            ], 200);
        }
        return response()->json(['message' => 'Access denied.'], 403);
    }
}
