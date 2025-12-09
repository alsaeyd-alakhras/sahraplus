<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ActiveDeviceService;
use App\Models\UserActiveDevice;

class ActiveDevicesController extends Controller
{
    protected ActiveDeviceService $service;


    public function __construct(ActiveDeviceService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $this->authorize('view', UserActiveDevice::class);
        if (request()->ajax()) return $this->service->datatableIndex(request());
        return view('dashboard.active_devices.index');
    }
    public function destroy($id)
    {
        $plan = UserActiveDevice::findOrFail($id);
        $this->authorize('delete', UserActiveDevice::class);
        $this->service->deleteById($plan->id);

        return request()->ajax()
            ? response()->json(['status' => true, 'message' => 'تم حذف التصنيف'])
            : redirect()->route('dashboard.users_subscription.index')->with('success', 'تم حذف التصنيف');
    }
}
