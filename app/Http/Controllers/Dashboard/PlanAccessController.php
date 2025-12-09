<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Season;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PlanContentAccessRequest;
use App\Models\Movie;
use App\Models\Category;
use App\Models\PlanContentAccess;
use App\Models\Series;
use App\Services\PlanAccessService;
use App\Models\SubscriptionPlan;

class PlanAccessController extends Controller
{
    protected PlanAccessService $service;
    protected $subscription_plans;



    public function __construct(PlanAccessService $service)
    {
        $this->service = $service;
        $this->subscription_plans = SubscriptionPlan::select('id', 'name_ar', 'name_en')->orderBy('name_ar')->get();;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('view', PlanContentAccess::class);
        if (request()->ajax()) return $this->service->datatableIndex(request());

        return view('dashboard.plan_access.index');
    }

    public function getFilterOptions(Request $request, $column)
    {
        return $this->service->getFilterOptions($request, $column);
    }

    public function create()
    {
        $this->authorize('create', PlanContentAccess::class);
        $planAccess = new PlanContentAccess();

        $subscription_plans = $this->subscription_plans;

        return view('dashboard.plan_access.create', compact('planAccess', 'subscription_plans'));
    }

    public function store(PlanContentAccessRequest $request)
    {
        // return $request;
        $this->authorize('create', PlanContentAccess::class);
        $this->service->save($request->validated());
        return redirect()->route('dashboard.plan_access.index')->with('success', 'تم إضافة تصنيف');
    }

    public function show(PlanContentAccess $sub)
    {
        $this->authorize('show', PlanContentAccess::class);
        return view('dashboard.plan_access.show', compact('sub'));
    }

    public function edit($id)
    {
        $this->authorize('update', PlanContentAccess::class);

        $planAccess = PlanContentAccess::findOrFail($id);
        $subscription_plans = $this->subscription_plans;

        $btn_label = "تعديل";
        return view('dashboard.plan_access.edit', compact('planAccess', 'btn_label', 'subscription_plans'));
    }

    public function update(PlanContentAccessRequest $request, $id)
    {
        $this->authorize('update', PlanContentAccess::class);
        $this->service->update($request->validated(), $id);
        return redirect()->route('dashboard.plan_access.index')->with('success', 'تم تعديل التصنيف');
    }

    public function destroy( $id)
    {
        $plan = PlanContentAccess::findOrFail($id);
        $this->authorize('delete', PlanContentAccess::class);
        $this->service->deleteById($plan->id);

        return request()->ajax()
            ? response()->json(['status' => true, 'message' => 'تم حذف التصنيف'])
            : redirect()->route('dashboard.plan_access.index')->with('success', 'تم حذف التصنيف');
    }

    public function getContents(Request $request)
    {
        $type = $request->type;
        $locale = app()->getLocale(); // 'ar' أو 'en'

        switch ($type) {
            case 'category':
                $contents = \App\Models\Category::select('id', 'name_ar', 'name_en')->get();
                break;
            case 'movie':
                $contents = \App\Models\Movie::select('id', 'title_ar', 'title_en')->get();
                break;
            case 'series':
                $contents = \App\Models\Series::select('id', 'title_ar', 'title_en')->get();
                break;
            default:
                $contents = collect();
        }

        // تحويل الاسم حسب اللغة
        $contents = $contents->map(function ($item) use ($locale, $type) {
            if ($type === 'category') {
                $item->name = $locale === 'ar' ? $item->name_ar : $item->name_en;
            } else {
                $item->name = $locale === 'ar' ? $item->title_ar : $item->title_en;
            }
            return $item;
        });

        return response()->json($contents);
    }
}
