<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Models\UserActiveDevice;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class MangeDevicesController extends Controller
{
    //POST /api/v1/movies
    // تسجيل الجهاز
    public function registerDevice(Request $request)
    {
        $request->validate([
            'profile_id' => 'nullable|exists:user_profiles,id',
            'ip_address' => 'nullable|ip',
        ]);

        $deviceId = Str::uuid();

        $device = UserActiveDevice::create([
            'user_id' => $request->user()->id,
            'profile_id' => $request->profile_id,
            'device_id' => $deviceId,
            'ip_address' => $request->ip_address ?? $request->ip(),
            'last_activity' => Carbon::now(),
            'is_active' => true,
        ]);

        return response()->json([
            'status' => 'success',
            'device_id' => $deviceId,
        ]);
    }

    //POST
    // Heartbeat لتحديث النشاط
    public function heartbeat(Request $request)
    {
        $request->validate([
            'device_id' => 'required|string|max:100',
        ]);

        $device = UserActiveDevice::where('user_id', $request->user()->id)
            ->where('device_id', $request->device_id)
            ->first();

        if (!$device) {
            return response()->json([
                'status' => 'error',
                'message' => 'Device not found'
            ], 404);
        }

        $device->update([
            'last_activity' => Carbon::now(),
            'is_active' => true,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Device activity updated'
        ]);
    }
}
