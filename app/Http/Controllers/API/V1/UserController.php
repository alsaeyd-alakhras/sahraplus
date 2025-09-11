<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Get current authenticated user info
     */
    public function me(Request $request): UserResource
    {
        $user = $request->user()->load(['profiles', 'sessions', 'notifications', 'country']);
        return new UserResource($user);
    }

    /**
     * Update authenticated user profile
     */
    public function update(Request $request)
    {
        try {
            $user = $this->userService->update($request->all(), Auth::guard('sanctum')->user()->id);
            return new UserResource($user);
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => __('controller.Something_went_wrong')], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Change user password (optional)
     */
    public function changePassword(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed'
        ]);

        try {
            $this->userService->changePassword(Auth::guard('sanctum')->user(), $request->current_password, $request->new_password);
            return response()->json(['message' => __('controller.Password_updated_successfully')], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Get user profiles
     */
    public function profiles(Request $request)
    {
        return response()->json($request->user()->profiles);
    }

    /**
     * Get user sessions
     */
    public function sessions(Request $request)
    {
        return response()->json($request->user()->sessions);
    }

    /**
     * Get user notifications
     */
    public function notifications(Request $request)
    {
        return response()->json($request->user()->notifications);
    }

    /**
     * Mark notification as read
     */
    public function markNotificationRead($id)
    {
        // Auth::guard('sanctum')->user()
        $user = User::find(Auth::guard('sanctum')->user()->id);
        $notification = $user->notifications()->findOrFail($id);
        $notification->update(['read_at' => now()]);
        return response()->json(['message' => __('controller.Notification_marked_as_read')]);
    }
}
