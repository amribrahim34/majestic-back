<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Http\Requests\Website\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str; // Add this line
use App\Repositories\Interfaces\Website\CartRepositoryInterface;


class AuthController extends Controller
{

    protected $cartRepository;

    public function __construct(CartRepositoryInterface $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = auth('sanctum')->user();
            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json([
                'user' => $user,
                'token' => $token,
            ], 200);
        }

        return response()->json(['message' => __('messages.login_error')], 401);
    }

    // Method to handle user logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => __('messages.logout_success')], 200);
    }

    public function register(RegisterRequest $request)
    {
        // Validate the request data
        $v = $request->validated();

        // Create user
        $user = $this->create($v);
        $this->cartRepository->transferGuestCart(session()->getId(), $user->id);
        // Implement any post-registration logic here, such as login or token generation
        auth()->login($user);
        $user = auth('sanctum')->user();
        $token = $user->createToken('authToken')->plainTextToken;

        // Return a successful response, e.g., user data or a redirect
        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }



    /**
     * Create a new user instance after a valid registration.
     */
    protected function create(array $data)
    {
        return User::create([
            'user_name' => $data['user_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'remember_token' => Str::random(10), // Or handle token generation differently
        ]);
    }
}
