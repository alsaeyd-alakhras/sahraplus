<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SystemSettingsResource;
use App\Services\SystemSettingService;
use Illuminate\Http\Request;

class SystemSettingsController extends Controller
{
    protected SystemSettingService $service;

    public function __construct(SystemSettingService $service)
    {
        $this->service = $service;
    }
    public function edit()
    {
        $data = $this->service->getFormData();
        $data['logo_url'] = asset('storage/' . $data['logo_url']);
        return new SystemSettingsResource($data);
    }
}
