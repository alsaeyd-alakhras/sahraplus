<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RegisterDeviceRequest;
use App\Http\Requests\Api\HeartbeatRequest;
use App\Models\UserActiveDevice;
use App\Services\Subscriptions\SubscriptionAccessService;
use App\Http\Resources\UserActiveDeviceResource;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    protected $accessService;

    public function __construct(SubscriptionAccessService $accessService)
    {
        $this->accessService = $accessService;
    }

    /**
     * Register a new device
     */
    public function registerDevice(RegisterDeviceRequest $request)
    {
        $user = $request->user();

        // Check if user can register a new device
        $canRegister = $this->accessService->canRegisterDevice($user);

        if (!$canRegister['allowed']) {
            return response()->json([
                'message' => 'Cannot register device',
                'reason' => $canRegister['reason'],
                'details' => $canRegister,
            ], 422);
        }

        // Check if device already exists
        $device = UserActiveDevice::where('user_id', $user->id)
            ->where('device_id', $request->device_id)
            ->first();

        if ($device) {
            // Update existing device
            $device->update([
                'profile_id' => $request->profile_id,
                'ip_address' => $request->ip_address ?? $request->ip(),
                'last_activity' => Carbon::now(),
                'is_active' => true,
            ]);

            return response()->json([
                'message' => 'Device updated successfully',
                'device' => new UserActiveDeviceResource($device),
            ]);
        }

        // Create new device
        $device = UserActiveDevice::create([
            'user_id' => $user->id,
            'profile_id' => $request->profile_id,
            'device_id' => $request->device_id,
            'ip_address' => $request->ip_address ?? $request->ip(),
            'last_activity' => Carbon::now(),
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Device registered successfully',
            'device' => new UserActiveDeviceResource($device),
        ], 201);
    }

    /**
     * Update device activity (heartbeat)
     */
    public function heartbeat(HeartbeatRequest $request)
    {
        $user = $request->user();

        $device = UserActiveDevice::where('user_id', $user->id)
            ->where('device_id', $request->device_id)
            ->first();

        if (!$device) {
            return response()->json([
                'message' => 'Device not found. Please register the device first.',
            ], 404);
        }

        // Check if user can start stream on this device
        $canStream = $this->accessService->canStartStream($user, $request->device_id);

        if (!$canStream['allowed']) {
            return response()->json([
                'message' => 'Cannot start stream',
                'reason' => $canStream['reason'],
                'details' => $canStream,
            ], 422);
        }

        // Update last activity
        $device->update([
            'last_activity' => Carbon::now(),
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Heartbeat received',
            'device' => new UserActiveDeviceResource($device),
        ]);
    }

    /**
     * Get user's active devices
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $devices = UserActiveDevice::where('user_id', $user->id)
            ->where('is_active', true)
            ->orderBy('last_activity', 'desc')
            ->get();

        return UserActiveDeviceResource::collection($devices);
    }

    /**
     * Deactivate a device
     */
    public function deactivate(Request $request, $deviceId)
    {
        $user = $request->user();

        $device = UserActiveDevice::where('user_id', $user->id)
            ->where('device_id', $deviceId)
            ->first();

        if (!$device) {
            return response()->json([
                'message' => 'Device not found',
            ], 404);
        }

        $device->update([
            'is_active' => false,
        ]);

        return response()->json([
            'message' => 'Device deactivated successfully',
        ]);
    }
}
