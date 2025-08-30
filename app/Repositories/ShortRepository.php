<?php

namespace App\Repositories;

use App\Models\Short;
use Illuminate\Database\Eloquent\Builder;

class ShortRepository
{
    public function __construct(protected Short $short) {}

    public function getQuery(): Builder
    {
        return $this->short->query();
    }

    public function getById(int $id): ?Short
    {
        return $this->short->find($id);
    }

    public function save(array $data): Short
    {
        return Short::create($data);
    }

    public function update(array $data, int $id): Short
    {
        $short = $this->short->findOrFail($id);
        $short->update($data);
        return $short;
    }

    public function delete(int $id): ?Short
    {
        $short = $this->short->find($id);
        if ($short) $short->delete();
        return $short;
    }
}
