<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SystemSettingsRequest;
use App\Models\SystemSetting;
use App\Services\SystemSettingService;

class SystemSettingsController extends Controller
{
    protected SystemSettingService $service;

    public function __construct(SystemSettingService $service)
    {
        $this->service = $service;
    }

    // صفحة واحدة للعرض/التعديل
    public function edit()
    {
        $this->authorize('update', SystemSetting::class);
        $setting = $this->service->getFormData();   // ← اسم المتغير settings
        $btn_label = 'تحديث';
        return view('dashboard.pages.settings', compact('setting', 'btn_label'));
    }

    public function update(SystemSettingsRequest $request)
    {
        $this->authorize('update', SystemSetting::class);
        $this->service->update($request->validated() + $request->only(['logoUpload','faviconUpload']));
        return redirect()->route('dashboard.settings.edit')->with('success', __('controller.Updated_item_successfully'));
    }
}
