<?php
namespace App\Services;

use App\Repositories\AdminRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Yajra\DataTables\Facades\DataTables;

class AdminService
{
	/**
     * @var AdminRepository $adminRepository
     */
    protected $adminRepository;

    /**
     * DummyClass constructor.
     *
     * @param AdminRepository $adminRepository
     */
    public function __construct(AdminRepository $adminRepository)
    {
        $this->adminRepository = $adminRepository;
    }

    /**
     * Get all adminRepository.
     *
     * @return String
     */
    public function datatableIndex(Request $request)
    {
        $admins = $this->adminRepository->getQuery();
        if ($request->column_filters) {
            foreach ($request->column_filters as $fieldName => $values) {
                if (!empty($values)) {
                    // تجاهل القيم الخاصة
                    $filteredValues = array_filter($values, function($value) {
                        return !in_array($value, ['الكل', 'all', 'All']);
                    });
                    if($fieldName == 'status' && !empty($filteredValues)) {
                        $admins->where('last_activity', '>=', now()->subMinutes(5));
                    }
                    // تطبيق الفلتر فقط إذا كان هناك قيم صالحة
                    if (!empty($filteredValues) && $fieldName != 'status') {
                        $admins->whereIn($fieldName, $filteredValues);
                    }
                }
            }
        }
        return DataTables::of($admins)
                ->addIndexColumn()  // إضافة عمود الترقيم التلقائي
                ->addColumn('status', function ($admin) {
                    return $admin->last_activity;
                })
                ->addColumn('edit', function ($admin) {
                    return $admin->id;
                })
                ->make(true);
    }

    public function getFilterOptions(Request $request, $column)
    {
        $query = $this->adminRepository->getQuery();

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

    /**
     * Get adminRepository by id.
     *
     * @param $id
     * @return String
     */
    public function getById(int $id)
    {
        return $this->adminRepository->getById($id);
    }

    /**
     * Validate adminRepository data.
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */
    public function save(array $data)
    {
        DB::beginTransaction();
        try{
            // upload avatar
            if(isset($data['avatarUpload'])){
                $avatar = collect($data)->file('avatarUpload');
                $path = $avatar->store('avatars','public');
                $data['avatar'] = $path;
            }

            // hash Password
            $data['password'] = Hash::make($data['password']);

            // Create Admin
            $admin = $this->adminRepository->save($data);

            // Add Abilities For Admin
            if(isset($data['abilities'])){
                foreach ($data['abilities'] as $role) {
                    $this->adminRepository->addAdminRole($role,$admin);
                }
            }
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            // throw $e; // in dev env
            return redirect()->back()->with('error', $e->getMessage());
        }
        return $admin;
    }

    /**
     * Update adminRepository data
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */
    public function update(array $data, int $id)
    {
        DB::beginTransaction();
        try {
            // $adminRepository = $this->adminRepository->update($data, $id);
            $admin = $this->adminRepository->getById($id);

            $adminOld = $admin->toArray();

            // Update Avatar
            $oldAvatar = $admin->avatar;
            if(isset($data['avatarUpload'])){
                if($oldAvatar != null){
                    Storage::disk('public')->delete($oldAvatar);
                }
                $avatar = collect($data)->file('avatarUpload');
                $path = $avatar->store('avatars','public');
                $data['avatar'] = $path;
            }else{
                $data['avatar'] = $admin->avatar;
            }

            // Update Admin Password
            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']); // لا تحدثه
            }

            // Update Admin Data
            $admin = $this->adminRepository->update($data,$id);

            // Update Role For Admin
            if(
                (!isset($data['settings_profile']) && $data['settings_profile'] == false)
                && isset($data['abilities'])
            ){
                if ($data['abilities'] != null) {
                    $role_old = $this->adminRepository->getAdminRoles($admin->id);
                    $role_new = $data['abilities'];
                    foreach ($role_old as $role) {
                        if (!in_array($role, $role_new)) {
                            $this->adminRepository->deleteAdminRole($role,$admin->id);
                        }
                    }
                    foreach ($role_new as $role) {
                        $role_f = $this->adminRepository->getAdminRole($role,$admin->id);
                        if ($role_f == null) {
                            $this->adminRepository->addAdminRole($role,$admin);
                        }else{
                            $role_f->update(['ability' => 'allow']);
                        }
                    }
                }else{
                    $this->adminRepository->deleteAdminRoles($admin->id);
                }
            }

            // record log For update Admin Data
            ActivityLogService::log(
                'Updated',
                'Admin',
                "تم تحديث المستخدم : {$admin->name}.",
                $adminOld,
                $admin->getChanges()
            );

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            // throw new InvalidArgumentException('Unable to update post data');
            return redirect()->back()->with('error', $e->getMessage());
        }
        return $admin;
    }

    /**
     * Delete adminRepository by id.
     *
     * @param $id
     * @return String
     */
    public function deleteById(int $id)
    {
        DB::beginTransaction();
        try {
            $admin = $this->adminRepository->getById($id);
            if($admin->avatar != null){
                Storage::disk('public')->delete($admin->avatar);
            }

            $adminRepository = $this->adminRepository->delete($id);
            DB::commit();
            return $adminRepository;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to delete post data');
        }
    }

}
