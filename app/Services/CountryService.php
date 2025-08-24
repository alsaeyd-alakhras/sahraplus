<?php

namespace App\Services;

use App\Repositories\CountryRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Yajra\DataTables\Facades\DataTables;

class CountryService
{
    /**
     * @var CountryRepository $countryRepository
     */
    protected $countryRepository;

    /**
     * @param CountryRepository $countryRepository
     */
    public function __construct(CountryRepository $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    /**
     * عرض قائمة الدول (للـ Datatable)
     */
    public function datatableIndex(Request $request)
    {
        $countries = $this->countryRepository->getQuery();

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

                    // دعم حالة is_active كنص عربي/قيمة منطقية
                    if ($fieldName === 'is_active') {
                        $map = [
                            'نشط' => 1, 'فعال' => 1, 'active' => 1, '1' => 1, 1 => 1, true => 1,
                            'غير نشط' => 0, 'غير فعال' => 0, 'inactive' => 0, '0' => 0, 0 => 0, false => 0,
                        ];
                        $bools = [];
                        foreach ($filteredValues as $v) {
                            $bools[] = $map[$v] ?? $v;
                        }
                        $countries->whereIn('is_active', $bools);
                        continue;
                    }

                    // الفلاتر العامة
                    $countries->whereIn($fieldName, $filteredValues);
                }
            }
        }

        return DataTables::of($countries)
            ->addIndexColumn() // رقم تسلسلي
           ->addColumn('is_active', function ($country) {
    return $country->is_active ? 'نشط' : 'غير نشط';
})
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
        $query = $this->countryRepository->getQuery();

        if ($column === 'is_active') {
            return response()->json([
                'نشط',
                'غير نشط',
            ]);
        }

        // تطبيق الفلاتر النشطة من أعمدة أخرى
        if ($request->active_filters) {
            foreach ($request->active_filters as $fieldName => $values) {
                if (!empty($values) && $fieldName !== $column) {
                    $filteredValues = array_filter($values, function ($value) {
                        return !in_array($value, ['الكل', 'all', 'All']);
                    });
                    if (!empty($filteredValues)) {
                        if ($fieldName === 'is_active') {
                            $map = [
                                'نشط' => 1, 'فعال' => 1, 'active' => 1, '1' => 1, 1 => 1, true => 1,
                                'غير نشط' => 0, 'غير فعال' => 0, 'inactive' => 0, '0' => 0, 0 => 0, false => 0,
                            ];
                            $bools = [];
                            foreach ($filteredValues as $v) {
                                $bools[] = $map[$v] ?? $v;
                            }
                            $query->whereIn('is_active', $bools);
                        } else {
                            $query->whereIn($fieldName, $filteredValues);
                        }
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

    /**
     * إرجاع دولة بالمعرّف
     */
    public function getById(int $id)
    {
        return $this->countryRepository->getById($id);
    }

    /**
     * إنشاء دولة جديدة
     */
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
            $data['is_active'] = array_key_exists('is_active', $data) ? (bool)$data['is_active'] : true;
            $data['sort_order'] = $data['sort_order'] ?? 0;

            // إنشاء السجل
            $country = $this->countryRepository->save($data);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
        return $country;
    }

    /**
     * تحديث دولة
     */
    public function update(array $data, int $id)
    {
        DB::beginTransaction();
        try {
            $country = $this->countryRepository->getById($id);
            $countryOld = $country->toArray();

            // رفع/تحديث صورة العلم (اختياري)
            $oldFlag = $country->flag_url;
            if (isset($data['flagUpload']) && $data['flagUpload'] instanceof \Illuminate\Http\UploadedFile) {
                if ($oldFlag) {
                    // حذف الصورة القديمة إذا كانت محفوظة محليًا
                    Storage::disk('public')->delete($oldFlag);
                }
                $path = $data['flagUpload']->store('flags', 'public');
                $data['flag_url'] = $path; // أو Storage::url($path)
            } else {
                // إبقاء القيمة القديمة إذا لم تُرسل جديدة
                $data['flag_url'] = $country->flag_url;
            }

            // ضبط القيم البوليانية/الافتراضية
            if (array_key_exists('is_active', $data)) {
                $data['is_active'] = (bool)$data['is_active'];
            } else {
                $data['is_active'] = (bool)$country->is_active;
            }
            $data['sort_order'] = $data['sort_order'] ?? $country->sort_order;

            // تحديث السجل
            $country = $this->countryRepository->update($data, $id);

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

    /**
     * حذف دولة بالمعرّف
     */
    public function deleteById(int $id)
    {
        DB::beginTransaction();
        try {
            $country = $this->countryRepository->getById($id);

            // حذف صورة العلم إن كانت محلية
            if ($country && $country->flag_url) {
                Storage::disk('public')->delete($country->flag_url);
            }

            $deleted = $this->countryRepository->delete($id);
            DB::commit();
            return $deleted;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to delete country');
        }
    }
}
