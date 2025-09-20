<?php

namespace App\Services;

use App\Models\Short;
use App\Models\Series;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Repositories\ShortRepository;
use App\Repositories\SeriesRepository;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ShortService
{
    public function __construct(private SeriesRepository $repo) {}

    public function datatableIndex(Request $request)
    {
        $query = $this->repo->getQuery();

        if ($request->column_filters) {
            foreach ($request->column_filters as $fieldName => $values) {
                if (!empty($values)) {
                    $filteredValues = array_filter($values, fn($v)=>!in_array($v,['الكل','all','All']));
                    if (!empty($filteredValues)) $query->whereIn($fieldName, $filteredValues);
                }
            }
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('edit', fn($m)=> $m->id)
            ->make(true);
    }

    public function getFilterOptions(Request $request, string $column)
    {
        $query = $this->repo->getQuery();

        if ($request->active_filters) {
            foreach ($request->active_filters as $fieldName => $values) {
                if (!empty($values) && $fieldName !== $column) {
                    $filteredValues = array_filter($values, fn($v)=>!in_array($v,['الكل','all','All']));
                    if (!empty($filteredValues)) $query->whereIn($fieldName, $filteredValues);
                }
            }
        }

        $uniqueValues = $query->whereNotNull($column)
            ->where($column,'!=','')->distinct()->pluck($column)->filter()->values()->toArray();

        return response()->json($uniqueValues);
    }

    public function save(array $data)
    {
        DB::beginTransaction();
        try {
            $data['created_by'] = $data['created_by'] ?? optional(Auth::guard('admin')->user())->id;

            // العلاقات المركبة
            $categoryIds = $data['category_ids'] ?? [];
            $cast        = $data['cast'] ?? [];
            unset($data['category_ids'], $data['cast']);

            // الصور: نفضّل *_out إذا غير فارغين (نفس منطق الفيلم)
            if (array_key_exists('poster_url_out', $data) && $data['poster_url_out'] !== null && $data['poster_url_out'] !== '') {
                $data['poster_url'] = $data['poster_url_out'];
            } else {
                $data['poster_url'] = $data['poster_url'] ?? null;
            }

            if (array_key_exists('backdrop_url_out', $data) && $data['backdrop_url_out'] !== null && $data['backdrop_url_out'] !== '') {
                $data['backdrop_url'] = $data['backdrop_url_out'];
            } else {
                $data['backdrop_url'] = $data['backdrop_url'] ?? null;
            }

            unset($data['poster_url_out'], $data['backdrop_url_out']);

            // slug
            $data['slug'] = Str::slug($data['title_en'] ?? $data['title_ar']);

            // إنشاء
            $series = $this->repo->save($data);

            // sync العلاقات
            $this->syncCategories($series, $categoryIds ?? []);
            $this->syncCast($series, $cast ?? []);

            DB::commit();
            return $series;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(array $data, int $id)
    {
        DB::beginTransaction();
        try {
            $series = $this->repo->getById($id);

            // العلاقات
            $categoryIds = $data['category_ids'] ?? [];
            $cast        = $data['cast'] ?? [];
            unset($data['category_ids'], $data['cast']);

            // الصور: نفضّل *_out إذا غير فارغين، وإلا نبقي القديمة
            if (array_key_exists('poster_url_out', $data) && $data['poster_url_out'] !== null && $data['poster_url_out'] !== '') {
                $data['poster_url'] = $data['poster_url_out'];
            } else {
                $data['poster_url'] = $data['poster_url'] ?? $series->poster_url;
            }

            if (array_key_exists('backdrop_url_out', $data) && $data['backdrop_url_out'] !== null && $data['backdrop_url_out'] !== '') {
                $data['backdrop_url'] = $data['backdrop_url_out'];
            } else {
                $data['backdrop_url'] = $data['backdrop_url'] ?? $series->backdrop_url;
            }

            unset($data['poster_url_out'], $data['backdrop_url_out']);

            // slug
            $data['slug'] = Str::slug($data['title_en'] ?? $data['title_ar']);

            // تحديث
            $series = $this->repo->update($data, $id);

            // sync العلاقات
            $this->syncCategories($series, $categoryIds ?? []);
            $this->syncCast($series, $cast ?? []);

            DB::commit();
            return $series;
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

    /** --- Helpers --- */

    private function syncCategories(Series $series, array $categoryIds): void
    {
        $ids = array_filter(array_map('intval', $categoryIds));
        $series->categories()->sync($ids);
    }

    private function syncCast(Series $series, array $castRows): void
    {
        // مثل الفيلم بالضبط: شخص واحد = صف واحد (دور واحد) بسبب مفهرسة الـsync على person_id
        $pivotData = [];
        foreach ($castRows as $row) {
            if (!isset($row['person_id'])) continue;
            $pivotData[$row['person_id']] = [
                'role_type'      => $row['role_type'] ?? 'actor',
                'character_name' => $row['character_name'] ?? null,
                'sort_order'     => $row['sort_order'] ?? 0,
            ];
        }
        $series->people()->sync($pivotData);
    }
}
