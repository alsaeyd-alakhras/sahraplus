<?php

namespace App\Repositories;

use App\Models\MovieCategory;
use Illuminate\Database\Eloquent\Builder;

class MovieCategoryRepository
{
    public function __construct(protected MovieCategory $category) {}

    public function getQuery(): Builder { return $this->category->query(); }

    public function getById(int $id): ?MovieCategory { return $this->category->find($id); }

    public function save(array $data): MovieCategory { return MovieCategory::create($data); }

    public function update(array $data, int $id): MovieCategory
    {
        $cat = $this->category->findOrFail($id);
        $cat->update($data);
        return $cat;
    }

    public function delete(int $id): ?MovieCategory
    {
        $cat = $this->category->find($id);
        if ($cat) $cat->delete();
        return $cat;
    }
}
