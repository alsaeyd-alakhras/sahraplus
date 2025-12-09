<?php

namespace App\Repositories;

use App\Models\UserActiveDevice;
use Illuminate\Database\Eloquent\Builder;


class ActiveDeviceRepository
{
    public function __construct(protected UserActiveDevice $user) {}

    public function getQuery(): Builder { return $this->user->query(); }

    public function getById(int $id): ?UserActiveDevice { return $this->user->find($id); }

    public function save(array $data): UserActiveDevice { return UserActiveDevice::create($data); }

    public function update(array $data, int $id): UserActiveDevice
    {
        $sub = $this->user->findOrFail($id);
        $sub->update($data);
        return $sub;
    }

    public function delete(int $id): ?UserActiveDevice
    {
        $sub = $this->user->find($id);
        if ($sub) $sub->delete();
        return $sub;
    }
}