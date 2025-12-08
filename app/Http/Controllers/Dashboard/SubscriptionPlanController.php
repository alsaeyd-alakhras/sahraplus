<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\SubscriptionPlanService;
use App\Http\Requests\SubscriptionPlanRequest;
use App\Models\Country;
use App\Models\PlanCountryPrice;
use App\Models\PlanLimitation;
use App\Models\SubscriptionPlan;
use App\Models\SystemSetting;
use PhpOffice\PhpSpreadsheet\Settings;

class SubscriptionPlanController extends Controller
{
    protected $billing_periodOptions;
    protected $video_qualityOptions;
    protected $countries;
    protected SubscriptionPlanService $service;


    public function __construct(SubscriptionPlanService $service)
    {
        $this->service = $service;
        $this->countries = Country::all();

        $this->billing_periodOptions = [
            'monthly' => __('admin.monthly'),
            'quarterly' => __('admin.quarterly'),
            'yearly' =>  __('admin.yearly'),
        ];
        $this->video_qualityOptions = [
            'sd' => 'sd',
            'hd' => 'hd',
            'uhd' =>  'uhd',
        ];
    }

    public function index()
    {
        $this->authorize('view', SubscriptionPlan::class);
        if (request()->ajax()) return $this->service->datatableIndex(request());
        return view('dashboard.subscription_plans.index');
    }

    public function getFilterOptions(Request $request, $column)
    {
        return $this->service->getFilterOptions($request, $column);
    }

    public function create()
    {
        $this->authorize('create', SubscriptionPlan::class);
        $sub = new SubscriptionPlan();

        $rates = $this->systemSetting('currency_rates');

        $billing_periodOptions = $this->billing_periodOptions;
        $video_qualityOptions = $this->video_qualityOptions;
        $countries = $this->countries;
        return view('dashboard.subscription_plans.create', compact('sub', 'rates', 'countries', 'billing_periodOptions', 'video_qualityOptions'));
    }

    public function store(SubscriptionPlanRequest $request)
    {
        // return $request;
        $this->authorize('create', SubscriptionPlan::class);

        $this->service->save($request->validated());
        return redirect()->route('dashboard.sub_plans.index')->with('success', 'تم إضافة تصنيف');
    }

    public function show(SubscriptionPlan $sub)
    {
        $this->authorize('show', SubscriptionPlan::class);
        return view('dashboard.subscription_plans.show', compact('sub'));
    }

    public function edit($id)
    {
        $rates = $this->systemSetting('currency_rates');
        $this->authorize('update', SubscriptionPlan::class);
        $billing_periodOptions = $this->billing_periodOptions;
        $video_qualityOptions = $this->video_qualityOptions;
        $sub = SubscriptionPlan::findOrFail($id);
        $countries = $this->countries;
        $btn_label = "تعديل";
        return view('dashboard.subscription_plans.edit', compact('sub', 'rates', 'countries', 'btn_label', 'billing_periodOptions', 'video_qualityOptions'));
    }

    public function update(SubscriptionPlanRequest $request, SubscriptionPlan $sub_plan)
    {
        //return $request;
        $this->authorize('update', SubscriptionPlan::class);
        $this->service->update($request->validated(), $sub_plan->id);
        return redirect()->route('dashboard.sub_plans.index')->with('success', 'تم تعديل التصنيف');
    }

    public function destroy($sub_plan)
    {
        $sub_plan = SubscriptionPlan::findOrFail($sub_plan);
        $this->authorize('delete', SubscriptionPlan::class);
        $this->service->deleteById($sub_plan->id);

        return request()->ajax()
            ? response()->json(['status' => true, 'message' => 'تم حذف التصنيف'])
            : redirect()->route('dashboard.sub_plans.index')->with('success', 'تم حذف التصنيف');
    }

    public function deleteCast($id)
    {
        $cast = PlanLimitation::find($id);
        // افترض اسم الموديل Cast
        if ($cast) {
            $cast->delete();
            return response()->json([
                'status' => true,
                'message' => 'تم حذف القيد بنجاح'
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'القيد غير موجود'
        ], 404);
    }

    public function countryRowPartial(Request $request)
    {
        $i = $request->i;
        $countries = $this->countries;
        return view('dashboard.subscription_plans.partials._countryPrices_row', compact('i', 'countries'));
    }

    public function limitationsRowPartial(Request $request)
    {
        $i = $request->i;
        return view('dashboard.subscription_plans.partials._cast_row', compact('i'));
    }

    public function delete_country($id)
    {
        $cast = PlanCountryPrice::find($id);
        // افترض اسم الموديل Cast
        if ($cast) {
            $cast->delete();
            return response()->json([
                'status' => true,
                'message' => 'تم حذف القيد بنجاح'
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'القيد غير موجود'
        ], 404);
    }

    function systemSetting($key, $default = null)
    {
        return SystemSetting::where('key', $key)->value('value') ?? $default;
    }
}