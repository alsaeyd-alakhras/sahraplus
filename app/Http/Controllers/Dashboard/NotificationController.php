<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('view', Notification::class);
        if(request()->ajax()) {
            return $this->notificationService->datatableIndex(request());
        }
        return view('dashboard.pages.notifications');
    }

    public function getFilterOptions(Request $request, $column)
    {
        return $this->notificationService->getFilterOptions($request, $column);
    }

    public function show($id)
    {
        $this->authorize('view', Notification::class);

        $notification = Notification::find($id);
        return response()->json([
            'type' => $notification->type,
            'message' => app()->getLocale() === 'ar' ? $notification->data : $notification->data_en,
            'read_at' => $notification->read_at,
            'created_at' => $notification->created_at->format('Y-m-d H:i'),
        ]);
    }

    /**
     * تعليم إشعار كمقروء
     */
    public function markAsRead($id)
    {
        $this->authorize('update', Notification::class);

        $notification = Notification::findOrFail($id);
        $notification->update(['read_at' => now()]);

        return back()->with('success', 'تم التعليم كمقروء');
    }

    /**
     * حذف إشعار
     */
    public function destroy($id)
    {
        $this->authorize('delete', Notification::class);

        $notification = Notification::findOrFail($id);
        $notification->delete();

        return back()->with('success', 'تم الحذف بنجاح');
    }
}
