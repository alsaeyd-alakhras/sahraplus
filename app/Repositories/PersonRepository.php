<?php

namespace App\Repositories;

use App\Models\Person;
use Illuminate\Database\Eloquent\Builder;

class PersonRepository
{
    public function __construct(protected Person $person) {}

    public function getQuery(): Builder
    {
        return $this->person->query();
    }

    public function getById(int $id): ?Person
    {
        return $this->person->find($id);
    }

    public function save(array $data): Person
    {
        return Person::create($data);
    }

    public function update(array $data, int $id): Person
    {
        $p = $this->person->findOrFail($id);
        $p->update($data);
        return $p;
    }

    public function delete(int $id): ?Person
    {
        $p = $this->person->find($id);
        if ($p) $p->delete();
        return $p;
    }
}
