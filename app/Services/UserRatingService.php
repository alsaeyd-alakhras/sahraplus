<?php

namespace App\Services;

use App\Repositories\UserRatingRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Yajra\DataTables\Facades\DataTables;

class UserRatingService
{
    /**
     * @var UserRatingRepository $UserRatingRepository
     */
    protected $UserRatingRepository;

    /**
     * @param UserRatingRepository $UserRatingRepository
     */
    public function __construct(UserRatingRepository $UserRatingRepository)
    {
        $this->UserRatingRepository = $UserRatingRepository;
    }


    public function datatableIndex(Request $request)
    {
        $users_rating = $this->UserRatingRepository->getQuery();

        // فلترة حسب أعمدة محددة
        if ($request->column_filters) {
            foreach ($request->column_filters as $fieldName => $values) {
                if (!empty($values)) {
                    $filteredValues = array_filter($values, fn($v) => !in_array($v, ['الكل', 'all', 'All']));

                    if (empty($filteredValues)) continue;

                    if ($fieldName === 'user_name') {
                        $users_rating->whereHas('user', function ($q) use ($filteredValues) {
                            $q->where(function ($query) use ($filteredValues) {
                                foreach ($filteredValues as $name) {
                                    // نقسم الاسم المرسل إلى جزئين إذا فيه مسافة
                                    $parts = explode(' ', $name, 2);
                                    $first = $parts[0] ?? '';
                                    $last = $parts[1] ?? '';
                                    $query->orWhere(function ($q2) use ($first, $last) {
                                        $q2->where('first_name', 'LIKE', "%{$first}%");
                                        if ($last) $q2->where('last_name', 'LIKE', "%{$last}%");
                                    });
                                }
                            });
                        });
                    } else {
                        $users_rating->whereIn($fieldName, $filteredValues);
                    }
                }
            }
        }


        // تجهيز DataTable
        return DataTables::of($users_rating)
            ->addIndexColumn() // رقم تسلسلي
            ->addColumn('edit', fn($user_rating) => $user_rating->id)
            ->addColumn('user_name', fn($row) => $row->user_name) // accessor

            ->make(true);
    }


    /**
     * خيارات الفلاتر لأعمدة معينة
     */
    public function getFilterOptions(Request $request, $column)
    {
        $query = $this->UserRatingRepository->getQuery();

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
        if ($column === 'user_name') {
            $users = DB::table('user_ratings')
                ->join('users', 'user_ratings.user_id', '=', 'users.id')
                ->whereNotNull('users.first_name')
                ->where('users.first_name', '!=', '')
                ->distinct()
                ->select(DB::raw("CONCAT(users.first_name, ' ', users.last_name) as user_name"))
                ->pluck('user_name')
                ->filter()
                ->values()
                ->toArray();

            return response()->json($users);
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
        return $this->UserRatingRepository->getById($id);
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
            $country = $this->UserRatingRepository->save($data);

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
            $country = $this->UserRatingRepository->getById($id);

            // تحديث السجل
            $country = $this->UserRatingRepository->update($data, $id);

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
            $deleted = $this->UserRatingRepository->delete($id);
            DB::commit();
            return $deleted;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to delete User Rating');
        }
    }
}
