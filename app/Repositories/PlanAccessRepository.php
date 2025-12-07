<?php

namespace App\Repositories;

use App\Models\PlanContentAccess;
use Illuminate\Database\Eloquent\Builder;


class PlanAccessRepository
{
    public function __construct(protected PlanContentAccess $plan) {}

    public function getQuery(): Builder { return $this->plan->query(); }

    public function getById(int $id): ?PlanContentAccess { return $this->plan->find($id); }

    public function save(array $data): PlanContentAccess { return PlanContentAccess::create($data); }

    public function update(array $data, int $id): PlanContentAccess
    {
        $sub = $this->plan->findOrFail($id);
        $sub->update($data);
        return $sub;
    }

    public function delete(int $id): ?PlanContentAccess
    {
        $sub = $this->plan->find($id);
        if ($sub) $sub->delete();
        return $sub;
    }
}