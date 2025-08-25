<?php

namespace App\Repositories;

use App\Models\Country;

class CountryRepository
{
    /**
     * @var Country
     */
    protected Country $country;

    /**
     * Country constructor.
     *
     * @param Country $country
     */
    public function __construct(Country $country)
    {
        $this->country = $country;
    }

    /**
     * Get all countries.
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all()
    {
        return $this->country->get();
    }

    /**
     * Get country by id.
     *
     * @param int $id
     * @return Country|null
     */
    public function getById(int $id)
    {
        return $this->country->find($id);
    }

    /**
     * Base query builder for countries.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getQuery()
    {
        return $this->country->query();
    }

    /**
     * Create a country.
     *
     * @param array $data
     * @return Country
     */
    public function save(array $data)
    {
        // الحقول المتوقعة حسب المايغريشن:
        // code, name_ar, name_en, dial_code, currency, flag_url, is_active, sort_order
        return Country::create($data);
    }

    /**
     * Update a country.
     *
     * @param array $data
     * @param int $id
     * @return Country
     */
    public function update(array $data, int $id)
    {
        $country = $this->country->find($id);
        $country->update($data);
        return $country;
    }

    /**
     * Delete a country.
     *
     * @param int $id
     * @return Country|null
     */
    public function delete(int $id)
    {
        $country = $this->country->find($id);
        if ($country) {
            $country->delete();
        }
        return $country;
    }

    /**
     * (اختياري) جلب دولة برمزها (SA, EG...)
     *
     * @param string $code
     * @return Country|null
     */
    public function findByCode(string $code)
    {
        return $this->country->where('code', strtoupper($code))->first();
    }

    /**
     * (اختياري) قائمة الدول النشطة فقط.
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function allActive()
    {
        return $this->country->where('is_active', true)
                             ->orderBy('sort_order')
                             ->get();
    }
}
