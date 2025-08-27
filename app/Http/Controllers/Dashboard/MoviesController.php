<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Movie;
use Illuminate\Http\Request;
use App\Services\MovieService;
use App\Http\Requests\MovieRequest;
use App\Http\Controllers\Controller;

class MoviesController extends Controller
{
     protected MovieService $movieService;

    public function __construct(MovieService $movieService)
    {
        $this->movieService = $movieService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('view', Movie::class);

        if (request()->ajax()) {
            return $this->movieService->datatableIndex(request());
        }

        return view('dashboard.movies.index');
    }

    /**
     * Return distinct values for column filters (for Datatable for example).
     */
    public function getFilterOptions(Request $request, $column)
    {
        return $this->movieService->getFilterOptions($request, $column);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize('create', Movie::class);
        $movie = new Movie();
        return view('dashboard.movies.create', compact('movie'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MovieRequest $request)
    {
        $this->authorize('create', Movie::class);

        $this->movieService->save(
            $request->validated() + $request->only(['posterUpload', 'backdropUpload'])
        );

        return redirect()
            ->route('dashboard.movies.index')
            ->with('success', 'تم إضافة فيلم جديد');
    }

    /**
     * Display the specified resource.
     */
    public function show(Movie $movie)
    {
        $this->authorize('show', Movie::class);
        return view('dashboard.movies.show', compact('movie'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Movie $movie)
    {
        $this->authorize('update', Movie::class);

        $btn_label = "تعديل";
        return view('dashboard.movies.edit', compact('movie', 'btn_label'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MovieRequest $request, Movie $movie)
    {
        $this->authorize('update', Movie::class);

        $this->movieService->update(
            $request->validated() + $request->only(['posterUpload', 'backdropUpload']),
            $movie->id
        );

        return redirect()
            ->route('dashboard.movies.index')
            ->with('success', 'تم تعديل بيانات الفيلم');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Movie $movie)
    {
        $this->authorize('delete', Movie::class);

        $this->movieService->deleteById($movie->id);

        return request()->ajax()
            ? response()->json(['status' => true, 'message' => 'تم حذف الفيلم'])
            : redirect()->route('dashboard.movies.index')->with('success', 'تم حذف الفيلم');
    }
}
