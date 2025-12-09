<?php

namespace App\Services;

use App\Models\PlanContentAccess;
use App\Repositories\PlanAccessRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class PlanAccessService
{
    public function __construct(private PlanAccessRepository $repo) {}

    public function datatableIndex(Request $request)
    {
        $locale = app()->getLocale();

        // جلب كل الصفوف مع العلاقات مرة واحدة
        $q = PlanContentAccess::with(['plan', 'category', 'movie', 'series']);

        return DataTables::of($q)
            ->addIndexColumn()
            ->addColumn('plan_id', fn($row) => $row->plan ? ($locale == 'ar' ? $row->plan->name_ar : $row->plan->name_en) : '')
            ->addColumn('content_type', fn($row) => [
                'category' => __('admin.category'),
                'movie'    => __('admin.movie'),
                'series'   => __('admin.series'),
            ][$row->content_type] ?? '')
            ->addColumn('content_id', function ($row) use ($locale) {
                switch ($row->content_type) {
                    case 'category':
                        return $row->category ? ($locale == 'ar' ? $row->category->name_ar : $row->category->name_en) : '';
                    case 'movie':
                        return $row->movie ? ($locale == 'ar' ? $row->movie->title_ar : $row->movie->title_en) : '';
                    case 'series':
                        return $row->series ? ($locale == 'ar' ? $row->series->title_ar : $row->series->title_en) : '';
                    default:
                        return '';
                }
            })
            ->addColumn('access_type', fn($row) => $row->access_type == 'allow' ? __('admin.allow') : __('admin.deny'))
            ->addColumn('edit', fn($row) => $row->id)
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

            $cat = $this->repo->save($data);

            DB::commit();
            return $cat;
        } catch (\Throwable $e) {
            DB::rollBack();
            return $e;
        }
    }

    public function update(array $data, int $id)
    {
        DB::beginTransaction();
        try {
            $cat = $this->repo->update($data, $id);
            DB::commit();
            return $cat;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
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