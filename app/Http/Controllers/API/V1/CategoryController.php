<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use App\Services\ProfileContextService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    protected ProfileContextService $profileContextService;

    public function __construct(ProfileContextService $profileContextService)
    {
        $this->profileContextService = $profileContextService;
    }

    // GET /api/v1/categories
    public function index(Request $request)
    {
        // لا نستخدم الكاش هنا لأن النتائج تعتمد على البروفايل
        $query = Category::select('id','name_ar','name_en','slug','is_kids');

        // تطبيق فلتر محتوى الأطفال إذا لزم الأمر
        $query = $this->profileContextService->applyKidsFilterIfNeeded($query, $request);

        $categories = $query->orderBy('name_ar')->get();

        return CategoryResource::collection($categories);
    }

    // GET /api/v1/categories/{id}
    public function show(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }

        // التحقق من أن التصنيف مناسب للبروفايل (لو كان طفل)
        $profile = $this->profileContextService->resolveProfile($request);
        if ($this->profileContextService->shouldApplyKidsFilter($profile)) {
            if (!$category->is_kids) {
                return response()->json(['message' => 'هذا التصنيف غير متاح لملفات الأطفال'], 403);
            }
        }

        return new CategoryResource($category);
    }
}
