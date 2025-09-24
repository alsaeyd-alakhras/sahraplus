<?php

namespace App\Repositories;

use App\Models\Episode;
use Illuminate\Database\Eloquent\Builder;

class EpisodeRepository
{
    public function __construct(private Episode $episode) {}

    public function getQuery(): Builder
    {
        return $this->episode->query();
    }

    public function getById(int $id): ?Episode
    {
        return $this->episode->find($id);
    }

    public function save(array $data): Episode
    {
        return $this->episode->create($data);
    }

    public function update(array $data, int $id): Episode
    {
        $ep = $this->episode->findOrFail($id);
        $ep->update($data);
        return $ep;
    }

    public function delete(int $id): ?Episode
    {
        $ep = $this->episode->find($id);
        if ($ep) $ep->delete();
        return $ep;
    }
}
