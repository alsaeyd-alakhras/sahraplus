<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\HomeSectionRequest;
use App\Models\HomeSection;
use App\Services\HomeSectionService;
use Illuminate\Http\Request;

class HomeSectionController extends Controller
{
    protected $platformOptions;
    protected $contentTypeOptions;

    protected HomeSectionService $service;

    public function __construct(HomeSectionService $service)
    {
        $this->service = $service;
        $this->platformOptions = [
            'mobile' => __('admin.mobile'),
            'web' => __('admin.web'),
            'both' => __('admin.both'),
        ];

        $this->contentTypeOptions = [
            'movie' => __('admin.movie'),
            'series' => __('admin.series'),
        ];
    }

    public function index()
    {
        $this->authorize('view', HomeSection::class);
        if (request()->ajax()) {
            return $this->service->datatableIndex(request());
        }

        return view('dashboard.home_sections.index');
    }

    public function getFilterOptions(Request $request, $column)
    {
        return $this->service->getFilterOptions($request, $column);
    }

    public function create()
    {
        $this->authorize('create', HomeSection::class);
        $section = new HomeSection;

        $platformOptions = $this->platformOptions;
        $contentTypeOptions = $this->contentTypeOptions;

        return view('dashboard.home_sections.create', compact('section', 'platformOptions', 'contentTypeOptions'));
    }

    public function store(HomeSectionRequest $request)
    {
        $this->authorize('create', HomeSection::class);
        $this->service->save($request->validated());

        return redirect()->route('dashboard.home_sections.index')->with('success', __('admin.SectionAddedSuccessfully'));
    }

    public function show(HomeSection $home_section)
    {
        $this->authorize('show', HomeSection::class);

        return view('dashboard.home_sections.show', compact('home_section'));
    }

    public function edit($id)
    {
        $this->authorize('update', HomeSection::class);
        $section = HomeSection::with('items')->findOrFail($id);
        $platformOptions = $this->platformOptions;
        $contentTypeOptions = $this->contentTypeOptions;
        $btn_label = __('admin.Edit');
        
        $sectionItems = $section->items
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'home_section_id' => $item->home_section_id,
                    'content_type' => $item->content_type,
                    'content_id' => $item->content_id,
                    'sort_order' => $item->sort_order,
                ];
            })
            ->toArray();

        return view('dashboard.home_sections.edit', compact('section', 'btn_label', 'platformOptions', 'contentTypeOptions', 'sectionItems'));
    }

    public function update(HomeSectionRequest $request, HomeSection $home_section)
    {
        $this->authorize('update', HomeSection::class);
        $this->service->update($request->validated(), $home_section->id);

        return redirect()->route('dashboard.home_sections.index')->with('success', __('admin.SectionUpdatedSuccessfully'));
    }

    public function destroy($home_section)
    {
        $home_section = HomeSection::findOrFail($home_section);
        $this->authorize('delete', HomeSection::class);
        $this->service->deleteById($home_section->id);

        return request()->ajax()
            ? response()->json(['status' => true, 'message' => __('admin.SectionDeletedSuccessfully')])
            : redirect()->route('dashboard.home_sections.index')->with('success', __('admin.SectionDeletedSuccessfully'));
    }

    public function sectionItemRowPartial(Request $request)
    {
        $i = $request->i;
        $contentTypeOptions = $this->contentTypeOptions;
        $row = [];

        return view('dashboard.home_sections.partials._sectionItem_row', compact('i', 'contentTypeOptions', 'row'));
    }
}

