<?php

namespace App\Repositories;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Eloquent\Builder;


class SubscriptionPlanRepository
{
    public function __construct(protected SubscriptionPlan $sub_plan) {}

    public function getQuery(): Builder { return $this->sub_plan->query(); }

    public function getById(int $id): ?SubscriptionPlan { return $this->sub_plan->find($id); }

    public function save(array $data): SubscriptionPlan { return SubscriptionPlan::create($data); }

    public function update(array $data, int $id): SubscriptionPlan
    {
        $sub = $this->sub_plan->findOrFail($id);
        $sub->update($data);
        return $sub;
    }

    public function delete(int $id): ?SubscriptionPlan
    {
        $sub = $this->sub_plan->find($id);
        if ($sub) $sub->delete();
        return $sub;
    }
}
