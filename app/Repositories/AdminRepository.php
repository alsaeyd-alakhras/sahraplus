<?php
namespace App\Repositories;

use App\Models\Admin;
use App\Models\AdminRole;

class AdminRepository
{
	 /**
     * @var Admin
     */
    protected Admin $admin;

    /**
     * Admin constructor.
     *
     * @param Admin $admin
     */
    public function __construct(Admin $admin)
    {
        $this->admin = $admin;
    }

    /**
     * Get all admin.
     *
     * @return Admin $admin
     */
    public function all()
    {
        return $this->admin->get();
    }

     /**
     * Get admin by id
     *
     * @param $id
     * @return mixed
     */
    public function getById(int $id)
    {
        return $this->admin->find($id);
    }
    public function getQuery()
    {
        return $this->admin->query();
    }

    /**
     * Save Admin
     *
     * @param $data
     * @return Admin
     */
     public function save(array $data)
    {
        return Admin::create($data);
    }
     /**
     * Update Admin
     *
     * @param $data
     * @return Admin
     */
    public function update(array $data, int $id)
    {
        $admin = $this->admin->find($id);
        $admin->update($data);
        return $admin;
    }

    /**
     * Delete Admin
     *
     * @param $data
     * @return Admin
     */
   	 public function delete(int $id)
    {
        $admin = $this->admin->find($id);
        $admin->delete();
        return $admin;
    }

    // AdminRole
    public function getAdminRoles($id)
    {
        return AdminRole::where('admin_id', $id)->pluck('role_name')->toArray();
    }
    public function getAdminRole($role_name,$id)
    {
        return AdminRole::where('admin_id', $id)->where('role_name', $role_name)->first();
    }

    public function addAdminRole($role,$admin)
    {
        return AdminRole::create([
            'role_name' => $role,
            'admin_id' => $admin->id,
            'ability' => 'allow',
        ]);
    }

    public function deleteAdminRole($role_name,$id)
    {
        AdminRole::where('admin_id', $id)->where('role_name', $role_name)->delete();
    }

    public function deleteAdminRoles($id)
    {
        AdminRole::where('admin_id', $id)->delete();
    }
}
