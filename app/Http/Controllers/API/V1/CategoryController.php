<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    // GET /api/v1/categories
    public function index()
    {
        $categories = Cache::remember('api:v1:categories', 3600, function () {
            return Category::select('id','name_ar','name_en','slug')->orderBy('name_ar')->get();
        });

        return CategoryResource::collection($categories);
    }

    // GET /api/v1/categories/{id}
    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }
        return new CategoryResource($category);
    }
}
