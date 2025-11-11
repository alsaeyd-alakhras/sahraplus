<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Services\UserRatingService;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRatingRequest;
use App\Models\UserRating;

class UserRatingController extends Controller
{
    protected UserRatingService $userRatingService;

    public function __construct(UserRatingService $userRatingService)
    {
        $this->userRatingService = $userRatingService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('view', UserRating::class);

        if (request()->ajax()) {
            return $this->userRatingService->datatableIndex(request());
        }

        return view('dashboard.user_ratings.index');
    }

    /**
     * Return distinct values for column filters (for Datatable for example).
     */
    public function getFilterOptions(Request $request, $column)
    {
        return $this->userRatingService->getFilterOptions($request, $column);
    }

    /**
     * Display the specified resource.
     */
    public function show(UserRating $userRating)
    {
        $this->authorize('show', UserRating::class);
        return view('dashboard.user_ratings.show', compact('userRating'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, UserRating $userRating)
    {
        $this->authorize('update', UserRating::class);

        $btn_label = "تعديل";
        return view('dashboard.user_ratings.edit', compact('userRating', 'btn_label'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserRating $userRating)
    {
        $request->validate([
            'status' => 'in:pending,approved,rejected'
        ]);
        $this->authorize('update', UserRating::class);

        $this->userRatingService->update(['status'=>$request->status , 'reviewed_at' => now(),],$userRating->id);

        return redirect()
            ->route('dashboard.userRatings.index')
            ->with('success', __('controller.Updated_item_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserRating $userRating)
    {
        $this->authorize('delete', UserRating::class);

        $this->userRatingService->deleteById($userRating->id);

        return request()->ajax()
            ? response()->json(['status' => true, 'message' => __('controller.Deleted_item_successfully')])
            : redirect()->route('dashboard.userRatings.index')->with('success', __('controller.Deleted_item_successfully'));
    }
}
