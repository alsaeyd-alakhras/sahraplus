<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Short;
use Illuminate\Http\Request;
use App\Models\MovieCategory;
use App\Services\ShortService;
use App\Http\Requests\ShortRequest;
use App\Http\Controllers\Controller;

class ShortController extends Controller
{
    protected ShortService $shortService;
    protected $statusOptions;

    public function __construct(ShortService $shortService)
    {
        $this->shortService = $shortService;
        $this->statusOptions = [
            'active'   => 'نشط',
            'inactive' => 'غير نشط',
        ];
    }

    public function index()
    {
        $this->authorize('view', Short::class);

        if (request()->ajax()) {
            return $this->shortService->datatableIndex(request());
        }

        return view('dashboard.shorts.index');
    }

    public function getFilterOptions(Request $request, $column)
    {
        return $this->shortService->getFilterOptions($request, $column);
    }

    public function create(Request $request)
    {
        $this->authorize('create', Short::class);

       
        $short = new Short();
        $allCategories = MovieCategory::select('id','name_ar','name_en')->orderBy('name_ar')->get();
        $statusOptions = $this->statusOptions;
        $aspectOptions = ['vertical' => 'عمودي', 'horizontal' => 'أفقي'];

        return view('dashboard.shorts.create', compact('short','allCategories','statusOptions','aspectOptions'));
    }

    public function store(ShortRequest $request)
    {

        //  dd($request->all());
        $this->authorize('create', Short::class);

        $this->shortService->save($request->validated());

        return redirect()->route('dashboard.shorts.index')
            ->with('success', __('controller.Created_item_successfully'));
    }

    public function show(Short $short)
    {
        $this->authorize('show', Short::class);
        return view('dashboard.shorts.show', compact('short'));
    }

    public function edit(Request $request, Short $short)
    {
        $this->authorize('update', Short::class);

        $short->load(['categories:id', 'videoFiles']);

        $btn_label = "تعديل";
        $statusOptions = $this->statusOptions;
        $aspectOptions = ['vertical' => 'عمودي', 'horizontal' => 'أفقي'];
        $allCategories = MovieCategory::select('id','name_ar','name_en')->orderBy('name_ar')->get();

        return view('dashboard.shorts.edit', compact('short','btn_label','statusOptions','aspectOptions','allCategories'));
    }

    public function update(ShortRequest $request, Short $short)
    {
        $this->authorize('update', Short::class);

        $this->shortService->update($request->validated(), $short->id);

        return redirect()->route('dashboard.shorts.index')
            ->with('success', __('controller.Updated_item_successfully'));
    }

    public function destroy(Request $request, Short $short)
    {
        $this->authorize('delete', Short::class);

        $this->shortService->deleteById($short->id);

        return request()->ajax()
            ? response()->json(['status' => true, 'message' => __('controller.Deleted_item_successfully')])
            : redirect()->route('dashboard.shorts.index')->with('success', __('controller.Deleted_item_successfully'));
    }

    // Partials (لصفوف الفيديو فقط)
    public function videoRowPartial(Request $request)
    {
        $i   = (int) $request->get('i', 0);
        $row = [];
        return view('dashboard.shorts.partials._video_row', compact('i', 'row'));
    }
}
