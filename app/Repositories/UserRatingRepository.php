<?php

namespace App\Repositories;

use App\Models\UserRating;
use Illuminate\Support\Facades\DB;

class UserRatingRepository
{
    /**
     * @var UserRating
     */
    protected UserRating $userRating;

    /**
     * UserRating constructor.
     *
     * @param UserRating $userRating
     */
    public function __construct(UserRating $userRating)
    {
        $this->userRating = $userRating;
    }

    /**
     * Get all countries.
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all()
    {
        return $this->userRating->get();
    }

    /**
     * Get UserRating by id.
     *
     * @param int $id
     * @return UserRating|null
     */
    public function getById(int $id)
    {
        return $this->userRating->find($id);
    }

    /**
     * Base query builder for countries.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getQuery()
    {
        return $this->userRating
               ->query();
    }

    /**
     * Create a UserRating.
     *
     * @param array $data
     * @return UserRating
     */
    public function save(array $data)
    {
      return UserRating::create($data);
    }

    /**
     * Update a UserRating.
     *
     * @param array $data
     * @param int $id
     * @return UserRating
     */
    public function update(array $data, int $id)
    {
        $userRating = $this->userRating->find($id);
        $userRating->update($data);
        return $userRating;
    }

    /**
     * Delete a UserRating.
     *
     * @param int $id
     * @return UserRating|null
     */
    public function delete(int $id): ? UserRating
    {
        $userRating = $this->userRating->find($id);
        if ($userRating) $userRating->delete();
        return $userRating;
    }

}