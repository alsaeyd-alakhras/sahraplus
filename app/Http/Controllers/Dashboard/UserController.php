<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('view', User::class);
        if(request()->ajax()) {
            return $this->userService->datatableIndex(request());
        }
        return view('dashboard.users.index');
    }

    public function getFilterOptions(Request $request, $column)
    {
        return $this->userService->getFilterOptions($request, $column);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize('create', User::class);
        $user = new User();
        return view('dashboard.users.create', compact('user'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        $this->authorize('create', User::class);
        $this->userService->save($request->validated());
        return redirect()->route('dashboard.users.index')->with('success', 'تم اضافة مستخدم جديد');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $this->authorize('show', User::class);
        return view('dashboard.users.show', compact('user'));
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, User $user)
    {
        $this->authorize('update', User::class);
        $btn_label = "تعديل";
        return view('dashboard.users.edit', compact('user', 'btn_label'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user)
    {
        $this->authorize('update', User::class);
        $this->userService->update($request->validated(),$user->id);
        return redirect()->route('dashboard.users.index')->with('success', 'تم تعديل المستخدم');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, User $user)
    {
        $this->authorize('delete', User::class);
        $this->userService->deleteById($user->id);
        return request()->ajax()
            ? response()->json([ 'status' => true, 'message' => 'تم حذف المستخدم' ])
            : redirect()->route('dashboard.users.index')->with('success', 'تم حذف المستخدم');
    }
}
