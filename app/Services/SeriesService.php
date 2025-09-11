<?php

namespace App\Services;

use App\Repositories\SeriesRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class SeriesService
{
    public function __construct(private SeriesRepository $repo) {}

    public function datatableIndex(Request $request)
    {
        $query = $this->repo->getQuery();

        if ($request->column_filters) {
            foreach ($request->column_filters as $fieldName => $values) {
                if (!empty($values)) {
                    // تجاهل القيم الخاصة
                    $filteredValues = array_filter($values, function($value) {
                        return !in_array($value, ['الكل', 'all', 'All']);
                    });

                    // تطبيق الفلتر فقط إذا كان هناك قيم صالحة
                    if (!empty($filteredValues)) {
                        $query->whereIn($fieldName, $filteredValues);
                    }
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
                    $filteredValues = array_filter($values, function($value) {
                        return !in_array($value, ['الكل', 'all', 'All']);
                    });

                    if (!empty($filteredValues)) {
                        $query->whereIn($fieldName, $filteredValues);
                    }
                }
            }
        }


        // جلب القيم الفريدة للعمود المطلوب
        $uniqueValues = $query->whereNotNull($column)
                            ->where($column, '!=', '')
                            ->distinct()
                            ->pluck($column)
                            ->filter()
                            ->values()
                            ->toArray();
        return response()->json($uniqueValues);
    }

    public function save(array $data)
    {
        DB::beginTransaction();
        try {
            // من أنشأ؟
            $data['created_by'] = $data['created_by'] ?? optional(Auth::guard('admin')->user())->id;

            // رفع الصور
            if($data['poster_url_out'] && $data['poster_url_out'] == "" && $data['poster_url_out'] == null){
                $data['poster_url'] = $data['poster_url_out'];
            }else{
                $data['poster_url'] = $data['poster_url'] ?? null;
            }
            if($data['backdrop_url_out'] && $data['backdrop_url_out'] == "" && $data['backdrop_url_out'] == null){
                $data['backdrop_url'] = $data['backdrop_url_out'];
            }else{
                $data['backdrop_url'] = $data['backdrop_url'] ?? null;
            }

            $data['slug'] = Str::slug($data['title_en'] ?? $data['title_ar']);

            $series = $this->repo->save($data);
            DB::commit();
            return $series;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
            return back()->with('danger',$e->getMessage());
        }
    }

    public function update(array $data, int $id)
    {
        DB::beginTransaction();
        try {
            $series = $this->repo->getById($id);

            // رفع الصور
            if($data['poster_url_out'] && $data['poster_url_out'] == "" && $data['poster_url_out'] == null){
                $data['poster_url'] = $data['poster_url_out'];
            }else{
                $data['poster_url'] = $data['poster_url'] ?? $series->poster_url;
            }
            if($data['backdrop_url_out'] && $data['backdrop_url_out'] == "" && $data['backdrop_url_out'] == null){
                $data['backdrop_url'] = $data['backdrop_url_out'];
            }else{
                $data['backdrop_url'] = $data['backdrop_url'] ?? $series->backdrop_url;
            }
            $data['slug'] = Str::slug($data['title_en'] ?? $data['title_ar']);

            $series = $this->repo->update($data,$id);
            DB::commit();
            return $series;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
            return back()->with('error',$e->getMessage());
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
