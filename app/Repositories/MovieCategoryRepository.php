<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;

class MovieCategoryRepository
{
    public function __construct(protected Category $category) {}

    public function getQuery(): Builder { return $this->category->query(); }

    public function getById(int $id): ?Category { return $this->category->find($id); }

    public function save(array $data): Category { return Category::create($data); }

    public function update(array $data, int $id): Category
    {
        $cat = $this->category->findOrFail($id);
        $cat->update($data);
        return $cat;
    }

    public function delete(int $id): ?Category
    {
        $cat = $this->category->find($id);
        if ($cat) $cat->delete();
        return $cat;
    }
}
