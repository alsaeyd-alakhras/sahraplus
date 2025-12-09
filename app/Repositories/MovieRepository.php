<?php

namespace App\Repositories;

use App\Models\Movie;
use Illuminate\Database\Eloquent\Builder;

class MovieRepository
{
    protected Movie $movie;

    public function __construct(Movie $movie)
    {
        $this->movie = $movie;
    }

    public function getQuery(): Builder
    {
        return $this->movie->query()->latest();
    }

    public function getById(int $id): ?Movie
    {
        return $this->movie->find($id);
    }

    public function save(array $data): Movie
    {
        return Movie::create($data);
    }

    public function update(array $data, int $id): Movie
    {
        $movie = $this->movie->findOrFail($id);
        $movie->update($data);
        return $movie;
    }

    public function delete(int $id): ?Movie
    {
        $movie = $this->movie->find($id);
        if ($movie) $movie->delete();
        return $movie;
    }

    public function findBySlug(string $slug): ?Movie
    {
        return $this->movie->where('slug',$slug)->first();
    }
}