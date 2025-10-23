<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\MovieCategory;
use App\Http\Resources\MovieCategoryResource;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    // GET /api/v1/categories
    public function index()
    {
        $categories = Cache::remember('api:v1:categories', 3600, function () {
            return MovieCategory::select('id','name_ar','name_en','slug')->orderBy('name_ar')->get();
        });

        return MovieCategoryResource::collection($categories);
    }

    // GET /api/v1/categories/{id}
    public function show($id)
    {
        $category = MovieCategory::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }
        return new MovieCategoryResource($category);
    }
}
