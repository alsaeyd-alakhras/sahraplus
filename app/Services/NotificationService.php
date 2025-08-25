<?php

namespace App\Services;

use App\Models\NotificationTemplate;
use App\Models\User;
use App\Repositories\NotificationRepository;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class NotificationService
{
    /**	/**
     * @var NotificationRepository $notificationRepository
     */
    protected $notificationRepository;

    /**
     * NotificationService constructor.
     *
     * @param NotificationRepository $notificationRepository
     */
    public function __construct(NotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }
    /*
     * Get all adminRepository.
     *
     * @return String
     */
    public function datatableIndex(Request $request)
    {
        $notifications = $this->notificationRepository->getQuery()->where('notifiable_id', Auth::guard('admin')->user()?->id);
        if ($request->column_filters) {
            foreach ($request->column_filters as $fieldName => $values) {
                if (!empty($values)) {
                    // تجاهل القيم الخاصة
                    $filteredValues = array_filter($values, function($value) {
                        return !in_array($value, ['الكل', 'all', 'All']);
                    });
                    if($fieldName == 'status' && !empty($filteredValues)) {
                        $notifications->where('last_activity', '>=', now()->subMinutes(5));
                    }
                    // تطبيق الفلتر فقط إذا كان هناك قيم صالحة
                    if (!empty($filteredValues) && $fieldName != 'status') {
                        $notifications->whereIn($fieldName, $filteredValues);
                    }
                }
            }
        }
        return DataTables::of($notifications)
                ->addIndexColumn()  // إضافة عمود الترقيم التلقائي
                ->addColumn('message', function ($admin) {
                    return app()->getLocale() == 'ar' ? $admin->data : $admin->data_en;
                })
                ->addColumn('edit', function ($admin) {
                    return $admin->id;
                })
                ->make(true);
    }

    public function getFilterOptions(Request $request, $column)
    {
        $query = $this->notificationRepository->getQuery();

        if($column == 'status'){
            return response()->json([
                'نشط',
                'غير نشط'
            ]);
        }

        // تطبيق الفلاتر النشطة من الأعمدة الأخرى
        if ($request->active_filters) {
            foreach ($request->active_filters as $fieldName => $values) {
                if (!empty($values) && $fieldName !== $column) {
                    $filteredValues = array_filter($values, function($value) {
                        return !in_array($value, ['الكل', 'all', 'All']);
                    });

                    if (!empty($filteredValues)) {
                        $query->whereIn($fieldName, $filteredValues);
                    }
                }
            }
        }

        // جلب القيم الفريدة للعمود المطلوب
        $uniqueValues = $query->whereNotNull($column)
                            ->where($column, '!=', '')
                            ->distinct()
                            ->pluck($column)
                            ->filter()
                            ->values()
                            ->toArray();

        return response()->json($uniqueValues);
    }

    public function sendFromTemplate(User $user, string $templateName, array $data = [])
    {
        $template = NotificationTemplate::where('name', $templateName)->first();

        if (!$template) return;

        $message_ar = $this->replaceVariables($template->template_ar, $data);
        $message_en = $this->replaceVariables($template->template_en, $data);

        $user->notifications()->create([
            'type' => $template->type,
            'data' => $message_ar,
            'data_en' => $message_en,
        ]);
    }

    private function replaceVariables($templateText, $data)
    {
        foreach ($data as $key => $value) {
            $templateText = str_replace(":$key", $value, $templateText);
        }
        return $templateText;
    }
}
