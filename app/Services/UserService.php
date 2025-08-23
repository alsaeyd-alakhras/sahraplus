<?php
namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;
use Yajra\DataTables\Facades\DataTables;

class UserService
{
	/**
     * @var UserRepository $userRepository
     */
    protected $userRepository;

    /**
     * DummyClass constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Get all userRepository.
     *
     * @return String
     */
    public function getAll()
    {
        return $this->userRepository->all();
    }
    public function datatableIndex(Request $request)
    {
        $users = $this->userRepository->getQuery();
        if ($request->column_filters) {
            foreach ($request->column_filters as $fieldName => $values) {
                if (!empty($values)) {
                    // تجاهل القيم الخاصة
                    $filteredValues = array_filter($values, function($value) {
                        return !in_array($value, ['الكل', 'all', 'All']);
                    });

                    // تطبيق الفلتر فقط إذا كان هناك قيم صالحة
                    if (!empty($filteredValues)) {
                        $users->whereIn($fieldName, $filteredValues);
                    }
                }
            }
        }
        return DataTables::of($users)
                ->addIndexColumn()  // إضافة عمود الترقيم التلقائي
                ->addColumn('profiles', function ($user) {
                    return $user->profiles->count();
                })
                ->addColumn('country', function ($user) {
                    return $user->country?->name ;
                })
                ->addColumn('edit', function ($user) {
                    return $user->id;
                })
                ->make(true);
    }

    public function getFilterOptions(Request $request, $column)
    {
        $query = $this->userRepository->getQuery();

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

    /**
     * Get userRepository by id.
     *
     * @param $id
     * @return String
     */
    public function getById(int $id)
    {
        return $this->userRepository->getById($id);
    }

    /**
     * Validate userRepository data.
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */
    public function save(array $data)
    {
        return $this->userRepository->save($data);
    }

    public function changePassword(User $user, string $currentPassword, string $newPassword)
    {
        if (!Hash::check($currentPassword, $user->password)) {
            throw new \Exception('Current password is incorrect');
        }
        $user->password = Hash::make($newPassword);
        $user->save();
    }
    /**
     * Update userRepository data
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */
    public function update(array $data, int $id)
    {
        DB::beginTransaction();
        try {
            $user = $this->userRepository->getById($id);
            if(isset($data['password'])){
                $data['password'] = Hash::make($data['password']);
            }else{
                $data['password'] = $user->password;
            }
            $userRepository = $this->userRepository->update($data, $id);
            DB::commit();
            return $userRepository;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to update post data');
        }
    }

    /**
     * Delete userRepository by id.
     *
     * @param $id
     * @return String
     */
    public function deleteById(int $id)
    {
        DB::beginTransaction();
        try {
            $userRepository = $this->userRepository->delete($id);
            DB::commit();
            return $userRepository;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to delete post data');
        }
    }

}
