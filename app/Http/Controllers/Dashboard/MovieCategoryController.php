<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Models\MovieCategory;
use App\Http\Controllers\Controller;
use App\Services\MovieCategoryService;
use App\Http\Requests\MovieCategoryRequest;

class MovieCategoryController extends Controller
{
    public function __construct(private MovieCategoryService $service) {}

    public function index()
    {
        $this->authorize('view', MovieCategory::class);
        if (request()->ajax()) return $this->service->datatableIndex(request());
        return view('dashboard.movie_categories.index');
    }

    public function getFilterOptions(Request $request, $column)
    {
        return $this->service->getFilterOptions($request, $column);
    }

    public function create()
    {
        $this->authorize('create', MovieCategory::class);
        $movie_category = new MovieCategory();
        return view('dashboard.movie_categories.create', compact('movie_category'));
    }

    public function store(MovieCategoryRequest $request)
    {
        $this->authorize('create', MovieCategory::class);
        $this->service->save($request->validated());
        return redirect()->route('dashboard.movie-categories.index')->with('success','تم إضافة تصنيف');
    }

    public function show(MovieCategory $movie_category)
    {
        $this->authorize('show', MovieCategory::class);
        return view('dashboard.movie_categories.show', compact('movie_category'));
    }

    public function edit(MovieCategory $movie_category)
    {
        $this->authorize('update', MovieCategory::class);
        $btn_label = "تعديل";
        return view('dashboard.movie_categories.edit', compact('movie_category','btn_label'));
    }

    public function update(MovieCategoryRequest $request, MovieCategory $movie_category)
    {
        $this->authorize('update', MovieCategory::class);
        $this->service->update($request->validated(), $movie_category->id);
        return redirect()->route('dashboard.movie-categories.index')->with('success','تم تعديل التصنيف');
    }

    public function destroy(MovieCategory $movie_category)
    {
        $this->authorize('delete', MovieCategory::class);
        $this->service->deleteById($movie_category->id);

        return request()->ajax()
            ? response()->json(['status'=>true,'message'=>'تم حذف التصنيف'])
            : redirect()->route('dashboard.movie-categories.index')->with('success','تم حذف التصنيف');
    }
}
