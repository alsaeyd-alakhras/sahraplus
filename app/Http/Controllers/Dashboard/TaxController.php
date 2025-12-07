<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\TaxService;
use App\Http\Requests\TaxRequest;
use App\Models\Tax;
use App\Models\SubscriptionPlan;

class TaxController extends Controller
{
    protected $subscription_plans;
    protected TaxService $service;


    public function __construct(TaxService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $this->authorize('view', Tax::class);
        if (request()->ajax()) {
            return $this->service->datatableIndex(request());
        }
        return view('dashboard.taxes.index');
    }

    public function getFilterOptions(Request $request, $column)
    {
        return $this->service->getFilterOptions($request, $column);
    }

    public function create()
    {
        $this->authorize('create', Tax::class);
        $tax = new Tax();
        return view('dashboard.taxes.create', compact('tax'));
    }

    public function store(TaxRequest $request)
    {
     //   return $request;
        $this->authorize('create', Tax::class);
        $this->service->save($request->validated());
        return redirect()->route('dashboard.taxes.index')->with('success', 'تم إضافة تصنيف');
    }

    public function show(Tax $tax)
    {
        $this->authorize('show', Tax::class);
        return view('dashboard.taxes.show', compact('tax'));
    }

    public function edit($id)
    {
        $this->authorize('update', Tax::class);

        $tax = Tax::findOrFail($id);

        $btn_label = "تعديل";
        return view('dashboard.taxes.edit', compact('tax', 'btn_label'));
    }

    public function update(TaxRequest $request, $id)
    {
        $this->authorize('update', Tax::class);
        $this->service->update($request->validated(), $id);
        return redirect()->route('dashboard.taxes.index')->with('success', 'تم تعديل التصنيف');
    }

    public function destroy(Tax $tax)
    {
        $this->authorize('delete', Tax::class);
        $this->service->deleteById($tax->id);

        return request()->ajax()
            ? response()->json(['status' => true, 'message' => 'تم حذف التصنيف'])
            : redirect()->route('dashboard.taxes.index')->with('success', 'تم حذف التصنيف');
    }
}
