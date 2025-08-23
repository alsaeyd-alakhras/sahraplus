<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminRequest;
use App\Models\ActivityLog;
use App\Models\Admin;
use App\Services\AdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    protected AdminService $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('view', Admin::class);
        if(request()->ajax()) {
            return $this->adminService->datatableIndex(request());
        }
        return view('dashboard.admins.index');
    }

    public function getFilterOptions(Request $request, $column)
    {
        return $this->adminService->getFilterOptions($request, $column);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize('create', Admin::class);
        $admin = new Admin();
        return view('dashboard.admins.create', compact('admin'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdminRequest $request)
    {
        $this->authorize('create', Admin::class);
        $this->adminService->save($request->validated());
        return redirect()->route('dashboard.admins.index')->with('success', 'تم اضافة مستخدم جديد');
    }

    /**
     * Display the specified resource.
     */
    public function show(Admin $admin)
    {
        if(Auth::guard('admin')->user()->id != $admin->id && !Auth::guard('admin')->user()->can('show', Admin::class)){
            abort(403);
        }
        $profile = Auth::guard('admin')->user()->id == $admin->id && !Auth::guard('admin')->user()->can('show', Admin::class) ? true : false;
        $logs = ActivityLog::where('admin_id',$admin->id)->orderBy('created_at','DESC')->paginate(20);
        return view('dashboard.admins.show', compact('admin','logs','profile'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function settings(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        if(Auth::guard('admin')->user()->id != $admin->id){
            abort(403);
        }
        $btn_label = "تعديل";
        $settings_profile = true;
        return view('dashboard.admins.settings', compact('admin', 'btn_label', 'settings_profile'));
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Admin $admin)
    {
        $this->authorize('update', Admin::class);

        // Check this account Admin
        if($admin->id == '1' && Auth::guard('admin')->user()->id != $admin->id){
            abort(403);
        }

        $btn_label = "تعديل";
        return view('dashboard.admins.edit', compact('admin', 'btn_label'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdminRequest $request, Admin $admin)
    {
        if(Auth::guard('admin')->user()->id != $admin->id && !$this->authorize('update', Admin::class)){
            abort(403);
        }

        $this->adminService->update($request->validated(),$admin->id);

        return $request->settings_profile
            ? redirect()->route('dashboard.home')->with('success', 'تم تعديل بياناتك الشخصية')
            : redirect()->route('dashboard.admins.index')->with('success', 'تم تعديل المستخدم');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Admin $admin)
    {
        $this->authorize('delete', Admin::class);

        $this->adminService->deleteById($admin->id);

        return request()->ajax()
            ? response()->json([ 'status' => true, 'message' => 'تم حذف المستخدم' ])
            : redirect()->route('dashboard.admins.index')->with('success', 'تم حذف المستخدم');
    }
}
