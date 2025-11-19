<?php

namespace App\Repositories;

use App\Models\Series;
use Illuminate\Database\Eloquent\Builder;

class SeriesRepository
{
    protected Series $series;

    public function __construct(Series $series)
    {
        $this->series = $series;
    }

    public function getQuery(): Builder
    {
        return $this->series->query()->latest();
    }

    public function getById(int $id): ?Series
    {
        return $this->series->find($id);
    }

    public function save(array $data): Series
    {
        return Series::create($data);
    }

    public function update(array $data, int $id): Series
    {
        $series = $this->series->findOrFail($id);
        $series->update($data);
        return $series;
    }

    public function delete(int $id): ?Series
    {
        $series = $this->series->find($id);
        if ($series) $series->delete();
        return $series;
    }

    public function findBySlug(string $slug): ?Series
    {
        return $this->series->where('slug',$slug)->first();
    }
}