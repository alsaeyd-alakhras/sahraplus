<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogService
{
    // Event Type: Created, Updated, Deleted, Login, Logout, Access Denied
    public static function log($eventType, $modelName, $message, $oldData = null, $newData = null,$admin_id =null ,$admin_name = null)
    {
        // الحصول على ip الجهاز
        $internalIp = session('internal_ip', 'IP not found');


        $admin = Auth::guard('admin')->user();
        // حفظ التفاصيل في قاعدة البيانات
        if($admin){
            ActivityLog::create([
                'admin_id' => $admin_id ?? $admin->id,
                'admin_name' => $admin_name ?? $admin->name ?? 'Guest',
                'ip_request' => Request::ip(),
                'ip_address' => $internalIp,
                'event_type' => $eventType,
                'model_name' => $modelName,
                'message' => $message,
                'old_data' => json_encode($oldData),
                'new_data' => json_encode($newData),
                'created_at' => now(),
            ]);
        }
    }
}
