<?php

namespace App\Repositories;

use App\Models\HomeSection;
use Illuminate\Database\Eloquent\Builder;


class HomeSectionRepository
{
    public function __construct(protected HomeSection $home_section) {}

    public function getQuery(): Builder { return $this->home_section->query(); }

    public function getById(int $id): ?HomeSection { return $this->home_section->find($id); }

    public function save(array $data): HomeSection { return HomeSection::create($data); }

    public function update(array $data, int $id): HomeSection
    {
        $section = $this->home_section->findOrFail($id);
        $section->update($data);
        return $section;
    }

    public function delete(int $id): ?HomeSection
    {
        $section = $this->home_section->find($id);
        if ($section) $section->delete();
        return $section;
    }
}

