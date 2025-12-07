<?php

namespace App\Repositories;

use App\Models\HomeBanner;
use Illuminate\Database\Eloquent\Builder;

class HomeBannerRepository
{
    public function __construct(protected HomeBanner $homeBanner) {}

    public function getQuery(): Builder 
    { 
        return $this->homeBanner->query(); 
    }

    public function getById(int $id): ?HomeBanner 
    { 
        return $this->homeBanner->find($id); 
    }

    public function save(array $data): HomeBanner 
    { 
        return HomeBanner::create($data); 
    }

    public function update(array $data, int $id): HomeBanner
    {
        $banner = $this->homeBanner->findOrFail($id);
        $banner->update($data);
        return $banner;
    }

    public function delete(int $id): ?HomeBanner
    {
        $banner = $this->homeBanner->find($id);
        if ($banner) $banner->delete();
        return $banner;
    }
}

