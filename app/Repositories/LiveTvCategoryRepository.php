<?php

namespace App\Repositories;

use App\Models\LiveTvCategory;
use Illuminate\Database\Eloquent\Builder;

class LiveTvCategoryRepository
{
    protected LiveTvCategory $liveTvCategory;

    public function __construct(LiveTvCategory $liveTvCategory)
    {
        $this->liveTvCategory = $liveTvCategory;
    }

    public function getQuery(): Builder
    {
        return $this->liveTvCategory->query();
    }

    public function getById(int $id): ?LiveTvCategory
    {
        return $this->liveTvCategory->find($id);
    }

    public function save(array $data): LiveTvCategory
    {
        return LiveTvCategory::create($data);
    }

    public function update(array $data, int $id): LiveTvCategory
    {
        $category = $this->liveTvCategory->findOrFail($id);
        $category->update($data);
        return $category;
    }

    public function delete(int $id): ?LiveTvCategory
    {
        $category = $this->liveTvCategory->find($id);
        if ($category) $category->delete();
        return $category;
    }

    public function findBySlug(string $slug): ?LiveTvCategory
    {
        return $this->liveTvCategory->where('slug', $slug)->first();
    }
}
