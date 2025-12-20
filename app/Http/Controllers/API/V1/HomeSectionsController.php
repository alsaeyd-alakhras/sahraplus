<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\HomeSectionResource;
use App\Models\HomeSection;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class HomeSectionsController extends Controller
{
    use ApiResponse;
    // GET /api/v1/home_sections

    public function index(Request $request)
    {
        $platform = $request->get('platform', 'both'); // mobile, web, both
        $isChildProfile = $request->boolean('is_child_profile', false);

        $sections = HomeSection::query()
            ->active()
            ->forPlatform($platform)
            ->forKids($isChildProfile)
            ->currentlyVisible()
            ->with(['items' => function ($query) {
                $query->orderBy('sort_order');
            }, 'items.movie', 'items.series'])
            ->orderBy('sort_order')
            ->get();

        if ($sections->isEmpty()) {
            return $this->error("Sections Not Found", 404);
        }

        return $this->success([
            "items" => HomeSectionResource::collection($sections),
            "count" => $sections->count(),
        ], "Get Sections Successfully");
    }

    // GET /api/v1/home_section/{id}
    public function show($id)
    {
        $section = HomeSection::with(['items' => function ($query) {
            $query->orderBy('sort_order');
        }, 'items.movie', 'items.series'])->find($id);

        if (!$section) {
            return $this->error("Section Not Found", 404);
        }

        return $this->success([
            "item" => new HomeSectionResource($section),
        ], "Get Section Successfully");
    }
}

