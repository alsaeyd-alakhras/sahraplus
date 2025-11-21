<?php

namespace App\Services;

use App\Repositories\LiveTvCategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class LiveTvCategoryService
{
    public function __construct(private LiveTvCategoryRepository $repo) {}

    public function datatableIndex(Request $request)
    {
        $q = $this->repo->getQuery()->withCount('channels');

        return DataTables::of($q)
            ->addIndexColumn()
            ->addColumn('is_featured', fn($c) => $c->is_featured ? 'مميز' : 'غير مميز')
            ->addColumn('is_active', fn($c) => $c->is_active ? 'نشط' : 'غير نشط')
            ->addColumn('edit', fn($c) => $c->id)
            ->filter(function ($query) use ($request) {
                // Apply column filters first
                if ($request->column_filters) {
                    foreach ($request->column_filters as $field => $values) {
                        $vals = array_values(array_filter((array)$values, fn($v) => !in_array($v, ['الكل', 'all', 'All'])));
                        if (!$vals) continue;

                        if ($field === 'is_active') {
                            $map = ['نشط' => 1, '1' => 1, 1 => 1, true => 1, 'غير نشط' => 0, '0' => 0, 0 => 0, false => 0];
                            $query->whereIn('is_active', array_map(fn($v) => $map[$v] ?? $v, $vals));
                        } elseif ($field === 'is_featured') {
                            $map = ['مميز' => 1, '1' => 1, 1 => 1, true => 1, 'غير مميز' => 0, '0' => 0, 0 => 0, false => 0];
                            $query->whereIn('is_featured', array_map(fn($v) => $map[$v] ?? $v, $vals));
                        } else {
                            $query->whereIn($field, $vals);
                        }
                    }
                }

                // Then apply search
                if ($search = $request->get('search')['value'] ?? null) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name_ar', 'like', "%{$search}%")
                            ->orWhere('name_en', 'like', "%{$search}%")
                            ->orWhere('slug', 'like', "%{$search}%")
                            ->orWhere('description_ar', 'like', "%{$search}%")
                            ->orWhere('description_en', 'like', "%{$search}%");
                    });
                }
            })
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

                if ($field === 'is_active') {
                    $map = ['نشط' => 1, '1' => 1, 1 => 1, true => 1, 'غير نشط' => 0, '0' => 0, 0 => 0, false => 0];
                    $q->whereIn('is_active', array_map(fn($v) => $map[$v] ?? $v, $vals));
                } elseif ($field === 'is_featured') {
                    $map = ['مميز' => 1, '1' => 1, 1 => 1, true => 1, 'غير مميز' => 0, '0' => 0, 0 => 0, false => 0];
                    $q->whereIn('is_featured', array_map(fn($v) => $map[$v] ?? $v, $vals));
                } else {
                    $q->whereIn($field, $vals);
                }
            }
        }

        if ($column === 'is_active') {
            return response()->json(['نشط', 'غير نشط']);
        }
        if ($column === 'is_featured') {
            return response()->json(['مميز', 'غير مميز']);
        }

        $unique = $q->whereNotNull($column)->where($column, '!=', '')
            ->distinct()->pluck($column)->filter()->values()->toArray();
        return response()->json($unique);
    }

    private function uniqueSlug(string $base, int $excludeId = null): string
    {
        $slug = Str::slug($base);
        $orig = $slug;
        $i = 1;

        $query = $this->repo->getQuery()->where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        while ($query->exists()) {
            $slug = $orig . '-' . $i++;
            $query = $this->repo->getQuery()->where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        }

        return $slug;
    }

    public function save(array $data)
    {
        DB::beginTransaction();
        try {
            // Generate slug
            $nameForSlug = $data['name_en'] ?? $data['name_ar'];
            $data['slug'] = !empty($data['slug']) ? $data['slug'] : $this->uniqueSlug($nameForSlug);

            // Handle icon_url upload
            if (isset($data['icon_url_out']) && $data['icon_url_out'] !== null && $data['icon_url_out'] !== '') {
                $data['icon_url'] = $data['icon_url_out'];
                unset($data['icon_url_out']);
            } elseif (!isset($data['icon_url']) || empty($data['icon_url'])) {
                $data['icon_url'] = null;
            }

            // Handle cover_image_url upload
            if (isset($data['cover_image_url_out']) && $data['cover_image_url_out'] !== null && $data['cover_image_url_out'] !== '') {
                $data['cover_image_url'] = $data['cover_image_url_out'];
                unset($data['cover_image_url_out']);
            } elseif (!isset($data['cover_image_url']) || empty($data['cover_image_url'])) {
                $data['cover_image_url'] = null;
            }

            // Clean up any remaining file inputs
            unset($data['icon_url_out'], $data['cover_image_url_out']);

            $category = $this->repo->save($data);
            DB::commit();
            return $category;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(array $data, int $id)
    {
        DB::beginTransaction();
        try {
            $category = $this->repo->getById($id);

            // Generate slug if provided or names changed
            if (!empty($data['slug'])) {
                $data['slug'] = $this->uniqueSlug($data['slug'], $id);
            } elseif (isset($data['name_en']) || isset($data['name_ar'])) {
                $nameForSlug = $data['name_en'] ?? $data['name_ar'] ?? $category->name_en ?? $category->name_ar;
                $data['slug'] = $this->uniqueSlug($nameForSlug, $id);
            }

            // Handle icon_url upload
            if (isset($data['icon_url_out']) && $data['icon_url_out'] !== null && $data['icon_url_out'] !== '') {
                $data['icon_url'] = $data['icon_url_out'];
                unset($data['icon_url_out']);
            } elseif (!isset($data['icon_url'])) {
                $data['icon_url'] = $category->icon_url;
            }

            // Handle cover_image_url upload
            if (isset($data['cover_image_url_out']) && $data['cover_image_url_out'] !== null && $data['cover_image_url_out'] !== '') {
                $data['cover_image_url'] = $data['cover_image_url_out'];
                unset($data['cover_image_url_out']);
            } elseif (!isset($data['cover_image_url'])) {
                $data['cover_image_url'] = $category->cover_image_url;
            }

            // Clean up any remaining file inputs
            unset($data['icon_url_out'], $data['cover_image_url_out']);

            $category = $this->repo->update($data, $id);
            DB::commit();
            return $category;
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

    public function export(Request $request)
    {
        $q = $this->repo->getQuery()->withCount('channels');

        // Apply same filters as datatable
        if ($request->column_filters) {
            foreach ($request->column_filters as $field => $values) {
                $vals = array_values(array_filter((array)$values, fn($v) => !in_array($v, ['الكل', 'all', 'All'])));
                if (!$vals) continue;

                if ($field === 'is_active') {
                    $map = ['نشط' => 1, '1' => 1, 1 => 1, true => 1, 'غير نشط' => 0, '0' => 0, 0 => 0, false => 0];
                    $q->whereIn('is_active', array_map(fn($v) => $map[$v] ?? $v, $vals));
                } elseif ($field === 'is_featured') {
                    $map = ['مميز' => 1, '1' => 1, 1 => 1, true => 1, 'غير مميز' => 0, '0' => 0, 0 => 0, false => 0];
                    $q->whereIn('is_featured', array_map(fn($v) => $map[$v] ?? $v, $vals));
                } else {
                    $q->whereIn($field, $vals);
                }
            }
        }

        $categories = $q->orderBy('sort_order', 'asc')->orderBy('id', 'desc')->get();

        $export = new \App\Exports\ModelExport(
            $categories,
            ['#', 'الاسم بالعربية', 'الاسم بالإنجليزية', 'الترتيب', 'مميز', 'نشط', 'عدد القنوات'],
            function ($cat, $index) {
                return [
                    $index + 1,
                    $cat->name_ar,
                    $cat->name_en,
                    $cat->sort_order,
                    $cat->is_featured ? 'نعم' : 'لا',
                    $cat->is_active ? 'نعم' : 'لا',
                    $cat->channels_count ?? 0
                ];
            }
        );

        return \Maatwebsite\Excel\Facades\Excel::download($export, 'live-tv-categories-' . now()->format('Y-m-d') . '.xlsx');
    }
}
