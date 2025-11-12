<?php

namespace App\Repositories;

use App\Models\Download;

class DownloadRepository
{
    /**
     * @var Download
     */
    protected Download $download;

    /**
     * UserRating constructor.
     *
     * @param Download $download
     */
    public function __construct(Download $download)
    {
        $this->download = $download;
    }

    /**
     * Get all countries.
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all()
    {
        return $this->download->get();
    }

    /**
     * Get UserRating by id.
     *
     * @param int $id
     * @return UserRating|null
     */
    public function getById(int $id)
    {
        return $this->download->find($id);
    }

    /**
     * Base query builder for countries.
     *dashboard
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getQuery()
    {
        return $this->download->query()
            ->whereIn('status', ['downloading', 'completed']);
    }

    /**
     * Create a UserRatingDownload.
     *
     * @param array $data
     * @return Download
     */
    public function save(array $data)
    {
      return Download::create($data);
    }

    /**
     * Update a Download.
     *
     * @param array $data
     * @param int $id
     * @return Download
     */
    public function update(array $data, int $id)
    {
        $download = $this->download->find($id);
        $download->update($data);
        return $download;
    }

    /**
     * Delete a Download.
     *
     * @param int $id
     * @return Download|null
     */
    public function delete(int $id): ?Download
    {
        $download = $this->download->find($id);
        if ($download) $download->delete();
        return $download;
    }

}
