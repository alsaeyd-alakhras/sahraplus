<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentsRequest;
use App\Models\Payments;
use App\Services\PaymentsService;

class PaymentsController extends Controller
{
    protected $subscription_plans;
    protected PaymentsService $service;
    public function __construct(PaymentsService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $this->authorize('view', Payments::class);
        if (request()->ajax()) {
            return $this->service->datatableIndex(request());
        }
        return view('dashboard.payments.index');
    }

    public function getFilterOptions(Request $request, $column)
    {
        return $this->service->getFilterOptions($request, $column);
    }

    public function show($id)
    {

        $pay = Payments::findOrFail($id);
        $this->authorize('show', $pay);
        $pay->load(['user', 'subscription']);

        return view('dashboard.payments.show', compact('pay'));
    }


    public function destroy($id)
    {
        $pay = Payments::findOrFail($id);
        $this->authorize('delete', Payments::class);
        $this->service->deleteById($pay->id);
        return request()->ajax()
            ? response()->json(['status' => true, 'message' => 'تم حذف التصنيف'])
            : redirect()->route('dashboard.payments.index')->with('success', 'تم حذف التصنيف');
    }
}
