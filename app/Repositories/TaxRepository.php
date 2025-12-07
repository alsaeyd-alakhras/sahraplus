<?php

namespace App\Repositories;

use App\Models\Tax;
use Illuminate\Database\Eloquent\Builder;

class TaxRepository
{
    public function __construct(protected Tax $tax) {}

    public function getQuery(): Builder { return $this->tax->query(); }

    public function getById(int $id): ?Tax { return $this->tax->find($id); }

    public function save(array $data): Tax { return Tax::create($data); }

    public function update(array $data, int $id): Tax
    {
        $sub = $this->tax->findOrFail($id);
        $sub->update($data);
        return $sub;
    }

    public function delete(int $id): ?Tax
    {
        $sub = $this->tax->find($id);
        if ($sub) $sub->delete();
        return $sub;
    }
}
