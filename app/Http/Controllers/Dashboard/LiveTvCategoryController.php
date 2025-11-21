<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\LiveTvCategory;
use Illuminate\Http\Request;
use App\Services\LiveTvCategoryService;
use App\Http\Controllers\Controller;
use App\Http\Requests\LiveTvCategoryRequest;
use Illuminate\Support\Facades\Storage;

class LiveTvCategoryController extends Controller
{
    protected LiveTvCategoryService $liveTvCategoryService;

    public function __construct(LiveTvCategoryService $liveTvCategoryService)
    {
        $this->liveTvCategoryService = $liveTvCategoryService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('view', LiveTvCategory::class);

        if (request()->ajax()) {
            return $this->liveTvCategoryService->datatableIndex(request());
        }

        return view('dashboard.live-tv-categories.index');
    }

    /**
     * Return distinct values for column filters (for Datatable for example).
     */
    public function getFilterOptions(Request $request, $column)
    {
        return $this->liveTvCategoryService->getFilterOptions($request, $column);
    }

    /**
     * Export all categories to Excel
     */
    public function export(Request $request)
    {
        $this->authorize('view', LiveTvCategory::class);
        return $this->liveTvCategoryService->export($request);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize('create', LiveTvCategory::class);
        $category = new LiveTvCategory();
        return view('dashboard.live-tv-categories.create', compact('category'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LiveTvCategoryRequest $request)
    {
        $this->authorize('create', LiveTvCategory::class);

        $data = $request->except('_token');

        // Handle file uploads
        if ($request->hasFile('icon_url_out')) {
            $data['icon_url_out'] = $request->file('icon_url_out')->store('live-tv/icons', 'public');
        } else {
            unset($data['icon_url_out']);
        }

        if ($request->hasFile('cover_image_url_out')) {
            $data['cover_image_url_out'] = $request->file('cover_image_url_out')->store('live-tv/covers', 'public');
        } else {
            unset($data['cover_image_url_out']);
        }

        $this->liveTvCategoryService->save($data);

        return redirect()
            ->route('dashboard.live-tv-categories.index')
            ->with('success', __('controller.Created_item_successfully'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, LiveTvCategory $liveTvCategory)
    {
        $this->authorize('update', LiveTvCategory::class);

        $category = $liveTvCategory;
        $btn_label = "تعديل";
        return view('dashboard.live-tv-categories.edit', compact('category', 'btn_label'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LiveTvCategoryRequest $request, LiveTvCategory $liveTvCategory)
    {
        $this->authorize('update', LiveTvCategory::class);

        $data = $request->except('_token', '_method');

        // Handle file uploads
        if ($request->hasFile('icon_url_out')) {
            $data['icon_url_out'] = $request->file('icon_url_out')->store('live-tv/icons', 'public');
            // Delete old icon if exists
            if ($liveTvCategory->icon_url && Storage::disk('public')->exists($liveTvCategory->icon_url)) {
                Storage::disk('public')->delete($liveTvCategory->icon_url);
            }
        } else {
            unset($data['icon_url_out']);
        }

        if ($request->hasFile('cover_image_url_out')) {
            $data['cover_image_url_out'] = $request->file('cover_image_url_out')->store('live-tv/covers', 'public');
            // Delete old cover if exists
            if ($liveTvCategory->cover_image_url && Storage::disk('public')->exists($liveTvCategory->cover_image_url)) {
                Storage::disk('public')->delete($liveTvCategory->cover_image_url);
            }
        } else {
            unset($data['cover_image_url_out']);
        }

        $this->liveTvCategoryService->update($data, $liveTvCategory->id);

        return redirect()
            ->route('dashboard.live-tv-categories.index')
            ->with('success', __('controller.Updated_item_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, LiveTvCategory $liveTvCategory)
    {
        $this->authorize('delete', LiveTvCategory::class);

        $this->liveTvCategoryService->deleteById($liveTvCategory->id);

        return request()->ajax()
            ? response()->json(['status' => true, 'message' => __('controller.Deleted_item_successfully')])
            : redirect()->route('dashboard.live-tv-categories.index')->with('success', __('controller.Deleted_item_successfully'));
    }
}
