<?php

namespace App\Services;

use App\Models\Country;
use App\Models\SubscriptionPlan;
use App\Repositories\SubscriptionPlanRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class SubscriptionPlanService
{
    public function __construct(private SubscriptionPlanRepository $repo) {}

    public function datatableIndex(Request $request)
    {
        $q = $this->repo->getQuery();

        if ($request->column_filters) {
            foreach ($request->column_filters as $field => $values) {
                $vals = array_values(array_filter((array)$values, fn($v) => !in_array($v, ['الكل', 'all', 'All'])));
                if (!$vals) continue;
                if ($field === 'is_active') {
                    $q->whereIn('is_active', array_map(fn($v) => $v == 'نشط' ? 1 : 0, $vals));
                } else {
                    $q->whereIn($field, $vals);
                }
            }
        }

        return DataTables::of($q)
            ->addIndexColumn()
            ->addColumn('is_active', fn($c) => $c->is_active ? 'نشط' : 'غير نشط')
            ->addColumn('edit', fn($c) => $c->id)
            ->make(true);
    }

    public function getFilterOptions(Request $request, string $column)
    {
        $q = $this->repo->getQuery();

        if ($request->active_filters) {
            foreach ($request->active_filters as $field => $values) {
                if ($field === $column) continue;
                $vals = array_values(array_filter((array)$values, fn($v) => !in_array($v, ['الكل', 'all', 'All'])));
                if (!$vals) continue;
                $q->whereIn($field, $vals);
            }
        }

        if ($column === 'is_active') return response()->json(['نشط', 'غير نشط']);

        $unique = $q->whereNotNull($column)->where($column, '!=', '')
            ->distinct()->pluck($column)->filter()->values()->toArray();
        return response()->json($unique);
    }

    private function uniqueSlug(string $base): string
    {
        $slug = Str::slug($base);
        $orig = $slug;
        $i = 1;
        while ($this->repo->getQuery()->where('slug', $slug)->exists()) {
            $slug = $orig . '-' . $i++;
        }
        return $slug;
    }

    public function save(array $data)
    {
        DB::beginTransaction();
        try {
            $nameForSlug = $data['name_en'] ?? $data['name_ar'] ?? '';
            if ($nameForSlug) $data['slug'] = $this->uniqueSlug($nameForSlug);
            $sub   = $data['cast']   ?? [];
            $country   = $data['countryPrices']   ?? [];
            $data['currency'] = 'SAR';
            $data['is_customize'] = $data['is_customize'] == 1 ? 1 : 0;
            $cat = $this->repo->save($data);
            if ($cat->is_customize == 1) {
                $this->syncCountryPrice($cat, $country ?? []);
            }
            $this->syncPlanLimitation($cat, $sub ?? []);

            DB::commit();
            return $cat;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }


    public function update(array $data, int $id)
    {
        DB::beginTransaction();
        try {
            $nameForSlug = $data['name_en'] ?? $data['name_ar'] ?? '';
            if ($nameForSlug) $data['slug'] = $this->uniqueSlug($nameForSlug);
            $sub   = $data['cast']   ?? [];
            $country   = $data['countryPrices']   ?? [];
            $data['currency'] = 'SAR';
            $data['is_customize'] = $data['is_customize'] == 1 ? 1 : 0;
            $cat = $this->repo->update($data, $id);
            if ($cat->is_customize == 1) {
                $this->syncCountryPrice($cat, $country ?? [], true);
            }
            $this->syncPlanLimitation($cat, $sub ?? [], true);

            DB::commit();
            return $cat;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function syncPlanLimitation(SubscriptionPlan $sub_plan, array $subs, bool $replace = false): void
    {
        if ($replace) {
            $sub_plan->limitations()->delete();
        }
        $payload = [];

        foreach ($subs as $row) {
            $payload[] = [
                'limitation_type'           => $row['limitation_type'] ?? '',
                'limitation_key'           => $row['limitation_key'] ?? '',
                'limitation_value' => $row['limitation_value'] ?? null,
                'limitation_unit'       => $row['limitation_unit']   ?? null,
                'description_ar'       => $row['description_ar']   ?? null,
                'description_en'       => $row['description_en']   ?? null,
            ];
        }

        if (empty($subs)) {
            $sub_plan->limitations()->delete();
        }

        if (!empty($payload)) {
            $sub_plan->limitations()->createMany($payload);
        }
    }

    private function syncCountryPrice(SubscriptionPlan $sub_plan, array $subs, bool $replace = false): void
    {
        if ($replace) {
            $sub_plan->countryPrices()->delete();
        }
        $payload = [];

        foreach ($subs as $row) {
            $country = Country::find($row['country_id']) ?? null;

            $payload[] = [
                'plan_id'           => $sub_plan->id ?? '',
                'country_id'           => $country->id ?? '',
                'currency' => $country->currency ?? null,
                'price_sar' => $row['price_sar'] ?? null,
                'price_currency' => $row['price_currency'] ?? null,
            ];
        }

        if (empty($subs)) {
            $sub_plan->countryPrices()->delete();
        }

        if (!empty($payload)) {
            $sub_plan->countryPrices()->createMany($payload);
        }
    }



    public function deleteById(int $id)
    {
        DB::beginTransaction();
        try {
            $deleted = $this->repo->delete($id);
            DB::commit();
            return $deleted;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
