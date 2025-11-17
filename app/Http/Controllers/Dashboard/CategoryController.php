<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use App\Http\Requests\CategoryRequest;

class CategoryController extends Controller
{
    public function __construct(private CategoryService $service) {}

    public function index()
    {
        $this->authorize('view', Category::class);
        if (request()->ajax()) return $this->service->datatableIndex(request());
        return view('dashboard.categories.index');
    }

    public function getFilterOptions(Request $request, $column)
    {
        return $this->service->getFilterOptions($request, $column);
    }

    public function create()
    {
        $this->authorize('create', Category::class);
        $movie_category = new Category();
        return view('dashboard.categories.create', compact('movie_category'));
    }

    public function store(CategoryRequest $request)
    {
        $this->authorize('create', Category::class);
        $this->service->save($request->validated());
        return redirect()->route('dashboard.movie-categories.index')->with('success','تم إضافة تصنيف');
    }

    public function show(Category $movie_category)
    {
        $this->authorize('show', Category::class);
        return view('dashboard.categories.show', compact('movie_category'));
    }

    public function edit($id)
    {
        $this->authorize('update', Category::class);
        $movie_category = Category::findOrFail($id);
        $btn_label = "تعديل";
        return view('dashboard.categories.edit', compact('movie_category','btn_label'));
    }

    public function update(CategoryRequest $request, Category $movie_category)
    {
        $this->authorize('update', Category::class);
        $this->service->update($request->validated(), $movie_category->id);
        return redirect()->route('dashboard.movie-categories.index')->with('success','تم تعديل التصنيف');
    }

    public function destroy(Category $movie_category)
    {
        $this->authorize('delete', Category::class);
        $this->service->deleteById($movie_category->id);

        return request()->ajax()
            ? response()->json(['status'=>true,'message'=>'تم حذف التصنيف'])
            : redirect()->route('dashboard.movie-categories.index')->with('success','تم حذف التصنيف');
    }
}
