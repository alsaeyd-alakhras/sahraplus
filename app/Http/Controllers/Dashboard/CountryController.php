<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Country;
use Illuminate\Http\Request;
use App\Services\CountryService;
use App\Http\Controllers\Controller;
use App\Http\Requests\CountryRequest;

class CountryController extends Controller
{
    protected CountryService $countryService;

    public function __construct(CountryService $countryService)
    {
        $this->countryService = $countryService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('view', Country::class);

        if (request()->ajax()) {
            return $this->countryService->datatableIndex(request());
        }

        return view('dashboard.countries.index');
    }

    /**
     * Return distinct values for column filters (for Datatable for example).
     */
    public function getFilterOptions(Request $request, $column)
    {
        return $this->countryService->getFilterOptions($request, $column);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize('create', Country::class);
        $country = new Country();
        return view('dashboard.countries.create', compact('country'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CountryRequest $request)
    {
        $this->authorize('create', Country::class);

        $this->countryService->save($request->validated());

        return redirect()
            ->route('dashboard.countries.index')
            ->with('success', __('controller.Created_item_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Country $country)
    {
        $this->authorize('show', Country::class);
        return view('dashboard.countries.show', compact('country'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Country $country)
    {
        $this->authorize('update', Country::class);

        $btn_label = "تعديل";
        return view('dashboard.countries.edit', compact('country', 'btn_label'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CountryRequest $request, Country $country)
    {
        $this->authorize('update', Country::class);

        $this->countryService->update($request->validated(), $country->id);

        return redirect()
            ->route('dashboard.countries.index')
            ->with('success', __('controller.Updated_item_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Country $country)
    {
        $this->authorize('delete', Country::class);

        $this->countryService->deleteById($country->id);

        return request()->ajax()
            ? response()->json(['status' => true, 'message' => __('controller.Deleted_item_successfully')])
            : redirect()->route('dashboard.countries.index')->with('success', __('controller.Deleted_item_successfully'));
    }
}
