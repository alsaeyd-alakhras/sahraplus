<?php

namespace App\Repositories;

use App\Models\UserSubscription;
use Illuminate\Database\Eloquent\Builder;


class UserSubscriptionRepository
{
    public function __construct(protected UserSubscription $user) {}

    public function getQuery(): Builder { return $this->user->query(); }

    public function getById(int $id): ?UserSubscription { return $this->user->find($id); }

    public function save(array $data): UserSubscription { return UserSubscription::create($data); }

    public function update(array $data, int $id): UserSubscription
    {
        $sub = $this->user->findOrFail($id);
        $sub->update($data);
        return $sub;
    }

    public function delete(int $id): ?UserSubscription
    {
        $sub = $this->user->find($id);
        if ($sub) $sub->delete();
        return $sub;
    }
}
