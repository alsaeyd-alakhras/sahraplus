<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Services\DownloadService;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRatingRequest;
use App\Models\Download;

class DownloadController extends Controller
{
    protected DownloadService $downloadService;

    public function __construct(DownloadService $downloadService)
    {
        $this->downloadService = $downloadService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('view', Download::class);

        if (request()->ajax()) {
            return $this->downloadService->datatableIndex(request());
        }

        return view('dashboard.downloads.index');
    }

    /**
     * Return distinct values for column filters (for Datatable for example).
     */
    public function getFilterOptions(Request $request, $column)
    {
        return $this->downloadService->getFilterOptions($request, $column);
    }

    /**
     * Display the specified resource.
     */
    public function show(Download $download)
    {
        $this->authorize('show', Download::class);
        return view('dashboard.downloads.show', compact('userRating'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Download $download)
    {
        $this->authorize('update', Download::class);

        $btn_label = "تعديل";
        return view('dashboard.downloads.edit', compact('userRating', 'btn_label'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Download $download)
    {
        $request->validate([
            'status' => 'in:pending,approved,rejected'
        ]);
        $this->authorize('update', Download::class);

        $this->downloadService->update(['status'=>$request->status], $download->id);

        return redirect()
            ->route('dashboard.downloads.index')
            ->with('success', __('controller.Updated_item_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Download $download)
    {
        $this->authorize('delete', Download::class);

        $this->downloadService->deleteById($download->id);

        return request()->ajax()
            ? response()->json(['status' => true, 'message' => __('controller.Deleted_item_successfully')])
            : redirect()->route('dashboard.downloads.index')->with('success', __('controller.Deleted_item_successfully'));
    }
}
