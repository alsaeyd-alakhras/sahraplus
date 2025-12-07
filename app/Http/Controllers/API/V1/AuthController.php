<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * Handle user login and issue Sanctum token
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => __('validation.validation_failed'),
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $credentials = $validator->validated();

        $user = User::where('email', $credentials['email'])->first();

        // التحقق من كلمة المرور
        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'error' => __('controller.Invalid_credentials'),
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => new UserResource($user),
        ], Response::HTTP_OK);
    }

    /**
     * Register a new user and issue Sanctum token
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'country_code' => 'nullable|string|max:2',
            'language' => 'nullable|string|max:5',
            'avatar' => 'nullable|image|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => __('validation.validation_failed'),
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        DB::beginTransaction();
        try {

            $data = $validator->validated();

            if (isset($data['avatar']) && $data['avatar'] != null) {
                $avatar = $data['avatar'];
                $avatar = $avatar->store('avatars');
            } else {
                $avatar = null;
            }

            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'] ?? null,
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($data['password']),
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'gender' => $data['gender'] ?? null,
                'country_code' => $data['country_code'] ?? null,
                'language' => $data['language'] ?? 'ar',
                'avatar' => $avatar ?? null,
                'is_active' => $data['is_active'] ?? true,
                'is_banned' => $data['is_banned'] ?? false,
                'email_notifications' => $data['email_notifications'] ?? true,
                'push_notifications' => $data['push_notifications'] ?? true,
                'parental_controls' => $data['parental_controls'] ?? false,
                'last_activity' => now(),
            ]);

            // Guest Profile
            $user->profiles()->create([
                'name' => 'Guest',
                'avatar_url' => null,
                'is_default' => true,
                'is_child_profile' => false,
                'pin_code' => null,
                'language' => 'ar',
                'is_active' => true,
            ]);

            // Child Profile
            $user->profiles()->create([
                'name' => 'Child',
                'avatar_url' => null,
                'is_default' => false,
                'is_child_profile' => true,
                'pin_code' => 1234,
                'language' => 'ar',
                'is_active' => true,
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            DB::commit();

            return response()->json([
                'token' => $token,
                'user' => new UserResource($user),
            ], Response::HTTP_CREATED);

        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => __('controller.Something_went_wrong'),
                'errors' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    /**
     * Logout by revoking the current token
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => __('controller.Logged_out_successfully')], Response::HTTP_OK);
    }
}
