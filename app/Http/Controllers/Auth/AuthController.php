<?php
declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(UserRegisterRequest $request): JsonResponse
    {
        $userData = $request->validated();
        $userData['role'] = UserRole::CUSTOMER;
        $user = User::create($userData);
        $token = JWTAuth::fromUser($user);

        return response()->json(['token' => $token], Response::HTTP_CREATED);
    }

    public function login(UserLoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        $user = User::where('email', $credentials['email'])->first();
        if (!$token = Auth::attempt($credentials)) {
            return response()->json(['error'=>'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }

        return response()->json(['token' => $token]);
    }

    public function logout(): JsonResponse
    {
        Auth::logout();
        return response()->json(['message' => 'Logged out successfully.']);
    }

    public function me(): JsonResponse
    {
        return response()->json(Auth::user());
    }
}
