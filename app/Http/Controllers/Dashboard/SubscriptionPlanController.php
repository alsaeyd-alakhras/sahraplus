<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubscriptionPlanRequest;
use App\Models\Country;
use App\Models\PlanCountryPrice;
use App\Models\SubscriptionPlan;
use App\Models\SystemSetting;
use App\Services\SubscriptionPlanService;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    protected $billing_periodOptions;
    protected $contentAccessOptions;
    protected $accessTypeOptions;

    protected $video_qualityOptions;

    protected $countries;

    protected SubscriptionPlanService $service;

    public function __construct(SubscriptionPlanService $service)
    {
        $this->service = $service;
        $this->countries = Country::all();
        $this->contentAccessOptions = [
            'category' => __('admin.category'),
            'movie' => __('admin.movie'),
            'series' => __('admin.series'),
        ];

        $this->accessTypeOptions = [
            'allow' => __('admin.allow'),
            'deny' => __('admin.deny'),
        ];

        $this->billing_periodOptions = [
            'monthly' => __('admin.monthly'),
            'quarterly' => __('admin.quarterly'),
            'yearly' => __('admin.yearly'),
        ];
        $this->video_qualityOptions = [
            'sd' => 'sd',
            'hd' => 'hd',
            'uhd' => 'uhd',
        ];
    }

    public function index()
    {
        $this->authorize('view', SubscriptionPlan::class);
        if (request()->ajax()) {
            return $this->service->datatableIndex(request());
        }

        return view('dashboard.subscription_plans.index');
    }

    public function getFilterOptions(Request $request, $column)
    {
        return $this->service->getFilterOptions($request, $column);
    }

    public function create()
    {
        $this->authorize('create', SubscriptionPlan::class);
        $sub = new SubscriptionPlan;

        $rates = $this->systemSetting('currency_rates');

        $billing_periodOptions = $this->billing_periodOptions;
        $video_qualityOptions = $this->video_qualityOptions;
        $countries = $this->countries;
        $contentAccessOptions = $this->contentAccessOptions;
        $accessTypeOptions = $this->accessTypeOptions;

        return view('dashboard.subscription_plans.create', compact('sub', 'rates', 'countries', 'billing_periodOptions', 'video_qualityOptions', 'contentAccessOptions', 'accessTypeOptions'));
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
        $contentAccessOptions = $this->contentAccessOptions;
        $accessTypeOptions = $this->accessTypeOptions;
        $btn_label = 'تعديل';
        $countryPrices = $sub->countryPrices
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'plan_id' => $p->plan_id,
                    'country_id' => $p->country_id,
                    'currency' => $p->currency,
                    'price_sar' => $p->price_sar,
                    'price_currency' => $p->price_currency,
                ];
            })
            ->toArray();
        $planContentAccess = $sub->contentAccess
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'plan_id' => $p->plan_id,
                    'content_type' => $p->content_type,
                    'content_id' => $p->content_id,
                    'access_type' => $p->access_type,
                ];
            })
            ->toArray();

        return view('dashboard.subscription_plans.edit', compact('sub', 'rates', 'countries', 'btn_label', 'billing_periodOptions', 'video_qualityOptions', 'countryPrices', 'planContentAccess', 'contentAccessOptions', 'accessTypeOptions'));
    }

    public function update(SubscriptionPlanRequest $request, SubscriptionPlan $sub_plan)
    {
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

    public function countryRowPartial(Request $request)
    {
        $i = $request->i;
        $countries = $this->countries;

        return view('dashboard.subscription_plans.partials._countryPrices_row', compact('i', 'countries'));
    }
    public function planAccessRowPartial(Request $request)
    {
        $i = $request->i;
        $contentAccessOptions = $this->contentAccessOptions;
        $accessTypeOptions = $this->accessTypeOptions;

        return view('dashboard.subscription_plans.partials._planAccess_row', compact('i', 'contentAccessOptions', 'accessTypeOptions'));
    }
    public function delete_country($id)
    {
        $cast = PlanCountryPrice::find($id);
        // افترض اسم الموديل Cast
        if ($cast) {
            $cast->delete();

            return response()->json([
                'status' => true,
                'message' => 'تم حذف القيد بنجاح',
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'القيد غير موجود',
        ], 404);
    }

    public function systemSetting($key, $default = null)
    {
        return SystemSetting::where('key', $key)->value('value') ?? $default;
    }
}
