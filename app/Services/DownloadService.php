<?php

namespace App\Services;

use App\Repositories\DownloadRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Yajra\DataTables\Facades\DataTables;

class DownloadService
{
    /**
     * @var DownloadRepository $downloadRepository
     */
    protected $downloadRepository;

    /**
     * @param DownloadRepository $downloadRepository
     */
    public function __construct(DownloadRepository $downloadRepository)
    {
        $this->downloadRepository = $downloadRepository;
    }

    /**
     * عرض قائمة الدول (للـ Datatable)
     */
    public function datatableIndex(Request $request)
    {
        $countries = $this->downloadRepository->getQuery();

        // فلترة حسب أعمدة محددة
        if ($request->column_filters) {
            foreach ($request->column_filters as $fieldName => $values) {
                if (!empty($values)) {
                    // تجاهل القيم العامة
                    $filteredValues = array_filter($values, function ($value) {
                        return !in_array($value, ['الكل', 'all', 'All']);
                    });

                    if (empty($filteredValues)) {
                        continue;
                    }

                    // الفلاتر العامة
                    $countries->whereIn($fieldName, $filteredValues);
                }
            }
        }

        return DataTables::of($countries)
            ->addIndexColumn() // رقم تسلسلي
            ->addColumn('edit', function ($country) {
                return $country->id;
            })
            ->make(true);
    }

    /**
     * خيارات الفلاتر لأعمدة معينة
     */
    public function getFilterOptions(Request $request, $column)
    {
        $query = $this->downloadRepository->getQuery();

        // تطبيق الفلاتر النشطة من أعمدة أخرى
        if ($request->active_filters) {
            foreach ($request->active_filters as $fieldName => $values) {
                if (!empty($values) && $fieldName !== $column) {
                    $filteredValues = array_filter($values, function ($value) {
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

    public function getById(int $id)
    {
        return $this->downloadRepository->getById($id);
    }


    public function save(array $data)
    {
        DB::beginTransaction();
        try {
            // رفع صورة العلم (اختياري): توقع حقل 'flagUpload'
            if (isset($data['flagUpload'])) {
                $file = $data['flagUpload'];
                if ($file instanceof \Illuminate\Http\UploadedFile) {
                    $path = $file->store('flags', 'public');
                    $data['flag_url'] = $path; // أو Storage::url($path) لو تحب رابطًا عامًا
                }
            }

            // تأمين القيم الافتراضية
            $data['sort_order'] = $data['sort_order'] ?? 0;

            // إنشاء السجل
            $country = $this->downloadRepository->save($data);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
        return $country;
    }


    public function update(array $data, int $id)
    {
        DB::beginTransaction();
        try {
            $country = $this->downloadRepository->getById($id);

            // تحديث السجل
            $country = $this->downloadRepository->update($data, $id);

            // لو عندك ActivityLogService ممكن تضيفه هنا
            // ActivityLogService::log('Updated','Country',"تم تحديث الدولة: {$country->name_ar}.", $countryOld, $country->getChanges());

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return redirect()->back()->with('error', $e->getMessage());
        }
        return $country;
    }

    public function deleteById(int $id)
    {
        DB::beginTransaction();
        try {
            $deleted = $this->downloadRepository->delete($id);
            DB::commit();
            return $deleted;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to delete User Rating');
        }
    }
}
