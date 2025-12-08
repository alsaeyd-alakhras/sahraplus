<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\UserSubscriptionService;
use App\Http\Requests\PlanLimitationRequest;
use App\Models\PlanLimitation;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;

class UserSubscriptionController extends Controller
{
    protected $subscription_plans;
    protected UserSubscriptionService $service;


    public function __construct(UserSubscriptionService $service)
    {
        $this->service = $service;
        $this->subscription_plans = SubscriptionPlan::select('id', 'name_ar', 'name_en')->orderBy('name_ar')->get();;
    }

    public function index()
    {
        $this->authorize('view', UserSubscription::class);
        if (request()->ajax()) return $this->service->datatableIndex(request());
        return view('dashboard.users_subscription.index');
    }

    public function getFilterOptions(Request $request, $column)
    {
        return $this->service->getFilterOptions($request, $column);
    }

    public function create()
    {
        $this->authorize('create', UserSubscription::class);
        $user_subscription = new UserSubscription();
        $subscription_plans = $this->subscription_plans;

        return view('dashboard.users_subscription.create', compact('user_subscription', 'subscription_plans'));
    }

    public function store(PlanLimitationRequest $request)
    {
        // return $request;
        $this->authorize('create', UserSubscription::class);
        $this->service->save($request->validated());
        return redirect()->route('dashboard.users_subscription.index')->with('success', 'تم إضافة تصنيف');
    }

    public function show($id)
    {
        $sub=UserSubscription::where('id',$id)->with(['plan', 'user'])->first();
        $this->authorize('show', UserSubscription::class);
        return view('dashboard.users_subscription.show', compact('sub'));
    }

    public function edit($id)
    {
        $this->authorize('update', UserSubscription::class);

        $planLimitation = UserSubscription::findOrFail($id);
        $subscription_plans = $this->subscription_plans;

        $btn_label = "تعديل";
        return view('dashboard.users_subscription.edit', compact('planLimitation', 'btn_label', 'subscription_plans'));
    }

    public function update(PlanLimitationRequest $request, $id)
    {
        $this->authorize('update', UserSubscription::class);
        $this->service->update($request->validated(), $id);
        return redirect()->route('dashboard.users_subscription.index')->with('success', 'تم تعديل التصنيف');
    }

    public function destroy(UserSubscription $plan)
    {
        $this->authorize('delete', UserSubscription::class);
        $this->service->deleteById($plan->id);

        return request()->ajax()
            ? response()->json(['status' => true, 'message' => 'تم حذف التصنيف'])
            : redirect()->route('dashboard.users_subscription.index')->with('success', 'تم حذف التصنيف');
    }
}
