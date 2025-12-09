<?php

namespace App\Repositories;

use App\Models\Payments;
use Illuminate\Database\Eloquent\Builder;

class PaymentsRepository
{
    public function __construct(protected Payments $pay) {}

    public function getQuery(): Builder { return $this->pay->query(); }

    public function getById(int $id): ?Payments { return $this->pay->find($id); }

    public function save(array $data): Payments { return Payments::create($data); }

    public function update(array $data, int $id): Payments
    {
        $sub = $this->pay->findOrFail($id);
        $sub->update($data);
        return $sub;
    }

    public function delete(int $id): ?Payments
    {
        $sub = $this->pay->find($id);
        if ($sub) $sub->delete();
        return $sub;
    }
}
