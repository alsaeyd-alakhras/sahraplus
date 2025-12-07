<?php

namespace App\Repositories;

use App\Models\PlanLimitation;
use Illuminate\Database\Eloquent\Builder;


class PlanLimitationRepository
{
    public function __construct(protected PlanLimitation $plan) {}

    public function getQuery(): Builder { return $this->plan->query(); }

    public function getById(int $id): ?PlanLimitation { return $this->plan->find($id); }

    public function save(array $data): PlanLimitation { return PlanLimitation::create($data); }

    public function update(array $data, int $id): PlanLimitation
    {
        $sub = $this->plan->findOrFail($id);
        $sub->update($data);
        return $sub;
    }

    public function delete(int $id): ?PlanLimitation
    {
        $sub = $this->plan->find($id);
        if ($sub) $sub->delete();
        return $sub;
    }
}
