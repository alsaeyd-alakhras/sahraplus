<?php

namespace App\Repositories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Builder;

class CouponRepository
{
    public function __construct(protected Coupon $coupon) {}

    public function getQuery(): Builder { return $this->coupon->query(); }

    public function getById(int $id): ?Coupon { return $this->coupon->find($id); }

    public function save(array $data): Coupon { return Coupon::create($data); }

    public function update(array $data, int $id): Coupon
    {
        $sub = $this->coupon->findOrFail($id);
        $sub->update($data);
        return $sub;
    }

    public function delete(int $id): ?Coupon
    {
        $sub = $this->coupon->find($id);
        if ($sub) $sub->delete();
        return $sub;
    }
}
