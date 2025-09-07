<?php


namespace App\Http\Controllers\API;

use App\Enums\RoleEnum;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only('logout');
    }

    private function registerWithRole(RegisterRequest $request, RoleEnum $role): JsonResponse
    {
        $validated = $request->validated();

        $user = User::create([
            ...$validated,
            'password' => Hash::make($validated['password']), // Ensure password is hashed
            'role' => $role->value,
            'timezone' => $validated['timezone'] ?? config('app.timezone'),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => new UserResource($user)
        ], 201);
}

    public function registerCustomer(RegisterRequest $request): JsonResponse
    {
        return $this->registerWithRole($request, RoleEnum::CUSTOMER);
    }

    public function registerProvider(RegisterRequest $request): JsonResponse
    {
        return $this->registerWithRole($request, RoleEnum::PROVIDER);
    }


    public function registerAdmin(RegisterRequest $request): JsonResponse
    {
        return $this->registerWithRole($request, RoleEnum::ADMIN);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'Invalid login credentials'
                ], 401);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => new UserResource($user)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Login failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(): JsonResponse
    {
        try {
            $currentToken = Auth::user()->currentAccessToken();

            if (!$currentToken) {
                return response()->json([
                    'message' => 'No active token found'
                ], 400);
            }

            $currentToken->delete();

            return response()->json([
                'message' => 'Logged out successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Logout failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
