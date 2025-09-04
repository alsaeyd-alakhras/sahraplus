<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Short;
use Illuminate\Http\Request;
use App\Services\ShortService;
use App\Http\Requests\ShortRequest;
use App\Http\Controllers\Controller;

class ShortController extends Controller
{
    protected ShortService $shortService;

    public function __construct(ShortService $shortService)
    {
        $this->shortService = $shortService;
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

    public function create()
    {
        $this->authorize('create', Short::class);
        $short = new Short();
        return view('dashboard.shorts.create', compact('short'));
    }

    public function store(ShortRequest $request)
    {
        $this->authorize('create', Short::class);
        $this->shortService->save($request->validated() + $request->only(['posterUpload','videoUpload']));
        return redirect()->route('dashboard.shorts.index')->with('success','تم إضافة فيديو قصير جديد');
    }

    public function show(Short $short)
    {
        $this->authorize('show', Short::class);
        return view('dashboard.shorts.show', compact('short'));
    }

    public function edit(Short $short)
    {
        $this->authorize('update', Short::class);
        $btn_label = "تعديل";
        return view('dashboard.shorts.edit', compact('short','btn_label'));
    }


    public function update(ShortRequest $request, Short $short)
    {
        $this->authorize('update', Short::class);
        $this->shortService->update($request->validated() + $request->only(['posterUpload','videoUpload']), $short->id);
        return redirect()->route('dashboard.shorts.index')->with('success','تم تعديل الفيديو');
    }

    public function destroy(Short $short)
    {
        $this->authorize('delete', Short::class);
        $this->shortService->deleteById($short->id);

        return request()->ajax()
            ? response()->json(['status'=>true,'message'=>'تم حذف الفيديو'])
            : redirect()->route('dashboard.shorts.index')->with('success','تم حذف الفيديو');
    }
}
