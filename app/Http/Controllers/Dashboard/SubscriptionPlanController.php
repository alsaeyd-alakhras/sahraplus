<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\SubscriptionPlanService;
use App\Http\Requests\SubscriptionPlanRequest;
use App\Models\PlanLimitation;
use App\Models\SubscriptionPlan;

class SubscriptionPlanController extends Controller
{
    protected $billing_periodOptions;
    protected $video_qualityOptions;
    protected SubscriptionPlanService $service;


    public function __construct( SubscriptionPlanService $service)
    {
        $this->service = $service;

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

        $billing_periodOptions = $this->billing_periodOptions;
        $video_qualityOptions = $this->video_qualityOptions;
        return view('dashboard.subscription_plans.create', compact('sub', 'billing_periodOptions', 'video_qualityOptions'));
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
        $this->authorize('update', SubscriptionPlan::class);
        $billing_periodOptions = $this->billing_periodOptions;
        $video_qualityOptions = $this->video_qualityOptions;
        $sub = SubscriptionPlan::findOrFail($id);
        $btn_label = "تعديل";
        return view('dashboard.subscription_plans.edit', compact('sub', 'btn_label', 'billing_periodOptions', 'video_qualityOptions'));
    }

    public function update(SubscriptionPlanRequest $request, SubscriptionPlan $sub_plan)
    {
        $this->authorize('update', SubscriptionPlan::class);
        $this->service->update($request->validated(), $sub_plan->id);
        return redirect()->route('dashboard.sub_plans.index')->with('success', 'تم تعديل التصنيف');
    }

    public function destroy(SubscriptionPlan $sub)
    {
        $this->authorize('delete', SubscriptionPlan::class);
        $this->service->deleteById($sub->id);

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
}
