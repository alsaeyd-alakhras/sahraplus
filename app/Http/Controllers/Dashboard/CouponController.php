<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\CouponService;
use App\Http\Requests\CouponRequest;
use App\Models\Coupon;
use App\Models\SubscriptionPlan;

class CouponController extends Controller
{
    protected $subscription_plans;
    protected CouponService $service;


    public function __construct(CouponService $service)
    {
        $this->service = $service;
        $this->subscription_plans = SubscriptionPlan::select('id', 'name_ar', 'name_en')->orderBy('name_ar')->get();;
    }

    public function index()
    {
        $this->authorize('view', Coupon::class);
        if (request()->ajax()) {
            return $this->service->datatableIndex(request());
        }
        return view('dashboard.coupons.index');
    }

    public function getFilterOptions(Request $request, $column)
    {
        return $this->service->getFilterOptions($request, $column);
    }

    public function create()
    {
        $this->authorize('create', Coupon::class);
        $coupon = new Coupon();
        $subscription_plans = $this->subscription_plans;

        return view('dashboard.coupons.create', compact('coupon', 'subscription_plans'));
    }

    public function store(CouponRequest $request)
    {
     //   return $request;
        $this->authorize('create', Coupon::class);
        $this->service->save($request->validated());
        return redirect()->route('dashboard.coupons.index')->with('success', 'تم إضافة تصنيف');
    }

    public function show(Coupon $sub)
    {
        $this->authorize('show', Coupon::class);
        return view('dashboard.coupons.show', compact('sub'));
    }

    public function edit($id)
    {
        $this->authorize('update', Coupon::class);

          $coupon = Coupon::findOrFail($id);
        $subscription_plans = $this->subscription_plans;

        $btn_label = "تعديل";
        return view('dashboard.coupons.edit', compact('coupon', 'btn_label', 'subscription_plans'));
    }

    public function update(CouponRequest $request, $id)
    {
        $this->authorize('update', Coupon::class);
        $this->service->update($request->validated(), $id);
        return redirect()->route('dashboard.coupons.index')->with('success', 'تم تعديل التصنيف');
    }

    public function destroy(Coupon $plan)
    {
        $this->authorize('delete', Coupon::class);
        $this->service->deleteById($plan->id);

        return request()->ajax()
            ? response()->json(['status' => true, 'message' => 'تم حذف التصنيف'])
            : redirect()->route('dashboard.coupons.index')->with('success', 'تم حذف التصنيف');
    }
}
