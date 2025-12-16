<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\UserActiveDevice;
use Carbon\Carbon;
use Illuminate\Container\Attributes\Auth;

class CheckActiveDevice
{
    public function handle(Request $request, Closure $next)
    {
        // $deviceId = $request->header('X-Device-ID') ?? $request->device_id;
        // if (!$deviceId) {
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => 'Device ID is required'
        //     ], 400);
        // }

        
        if (Auth('web')->check()) {
            $user = Auth('web')->user()->id;

            $device = UserActiveDevice::where('user_id', $user)
                // ->where('device_id', $deviceId)
                ->where('is_active', true)
                ->first();

            if (!$device) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Device not registered or inactive'
                ], 403);
            }
            // تحديث آخر نشاط تلقائيًا
            $device->update(['last_activity' => Carbon::now()]);
        }
        if (Auth('sanctum')->check()) {

            $user = Auth('sanctum')->user()->id;

            $device = UserActiveDevice::where('user_id', $user)
                // ->where('device_id', $deviceId)
                ->where('is_active', true)
                ->first();

            if (!$device) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Device not registered or inactive'
                ], 403);
            }
            $device->update(['last_activity' => Carbon::now()]);
        }
        return $next($request);
    }
}
