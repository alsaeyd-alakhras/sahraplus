<?php

namespace App\Services;

use App\Repositories\CategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class CategoryService
{
    public function __construct(private CategoryRepository $repo) {}

    public function datatableIndex(Request $request)
    {
        $q = $this->repo->getQuery();

        if ($request->column_filters) {
            foreach ($request->column_filters as $field => $values) {
                $vals = array_values(array_filter((array)$values, fn($v)=>!in_array($v,['الكل','all','All'])));
                if (!$vals) continue;
                if ($field === 'is_active') {
                    $q->whereIn('is_active', array_map(fn($v)=> $v=='نشط'?1:0, $vals));
                } else {
                    $q->whereIn($field, $vals);
                }
            }
        }

        return DataTables::of($q)
            ->addIndexColumn()
            ->addColumn('is_active', fn($c)=> $c->is_active ? 'نشط' : 'غير نشط')
            ->addColumn('edit', fn($c)=> $c->id)
            ->make(true);
    }

    public function getFilterOptions(Request $request, string $column)
    {
        $q = $this->repo->getQuery();

        if ($request->active_filters) {
            foreach ($request->active_filters as $field=>$values) {
                if ($field===$column) continue;
                $vals = array_values(array_filter((array)$values, fn($v)=>!in_array($v,['الكل','all','All'])));
                if (!$vals) continue;
                $q->whereIn($field,$vals);
            }
        }

        if ($column==='is_active') return response()->json(['نشط','غير نشط']);

        $unique = $q->whereNotNull($column)->where($column,'!=','')
                    ->distinct()->pluck($column)->filter()->values()->toArray();
        return response()->json($unique);
    }

    private function uniqueSlug(string $base): string
    {
        $slug = Str::slug($base);
        $orig = $slug; $i=1;
        while ($this->repo->getQuery()->where('slug',$slug)->exists()) {
            $slug = $orig.'-'.$i++;
        }
        return $slug;
    }

    public function save(array $data)
    {
        DB::beginTransaction();
        try {
            $nameForSlug = $data['name_en'] ?: $data['name_ar'];
            $data['slug'] = $data['slug'] ?: $this->uniqueSlug($nameForSlug);

            if (array_key_exists('image_url_out', $data) && $data['image_url_out'] !== null && $data['image_url_out'] !== '') {
                $data['image_url'] = $data['image_url_out'];
            } else {
                $data['image_url'] = $data['image_url'] ?? null;
            }

            $cat = $this->repo->save($data);
            DB::commit();
            return $cat;
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error',$e->getMessage());
        }
    }

    public function update(array $data, int $id)
    {
        DB::beginTransaction();
        try {

            $nameForSlug = $data['name_en'] ?? $data['name_ar'] ?? '';
            if ($nameForSlug) $data['slug'] = $this->uniqueSlug($nameForSlug);

            if (array_key_exists('image_url_out', $data) && $data['image_url_out'] !== null && $data['image_url_out'] !== '') {
                $data['image_url'] = $data['image_url_out'];
            } else {
                $data['image_url'] = $data['image_url'] ?? null;
            }

            $cat = $this->repo->update($data,$id);
            DB::commit();
            return $cat;
        } catch (\Throwable $e) {
            DB::rollBack();
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