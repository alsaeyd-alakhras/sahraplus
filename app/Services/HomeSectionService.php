<?php

namespace App\Services;

use App\Models\HomeSection;
use App\Repositories\HomeSectionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class HomeSectionService
{
    public function __construct(private HomeSectionRepository $repo) {}

    public function datatableIndex(Request $request)
    {
        $q = $this->repo->getQuery();

        if ($request->column_filters) {
            foreach ($request->column_filters as $field => $values) {
                $vals = array_values(array_filter((array)$values, fn($v) => !in_array($v, [__('admin.All'), 'all', 'All'])));
                if (!$vals) continue;
                if ($field === 'is_active') {
                    $q->whereIn('is_active', array_map(fn($v) => $v == __('admin.Active') ? 1 : 0, $vals));
                } elseif ($field === 'is_kids') {
                    $q->whereIn('is_kids', array_map(fn($v) => $v == __('admin.Yes') ? 1 : 0, $vals));
                } else {
                    $q->whereIn($field, $vals);
                }
            }
        }

        return DataTables::of($q)
            ->addIndexColumn()
            ->addColumn('is_active', fn($c) => $c->is_active ? __('admin.Active') : __('admin.Inactive'))
            ->addColumn('is_kids', fn($c) => $c->is_kids ? __('admin.Yes') : __('admin.No'))
            ->addColumn('edit', fn($c) => $c->id)
            ->make(true);
    }

    public function getFilterOptions(Request $request, string $column)
    {
        $q = $this->repo->getQuery();

        if ($request->active_filters) {
            foreach ($request->active_filters as $field => $values) {
                if ($field === $column) continue;
                $vals = array_values(array_filter((array)$values, fn($v) => !in_array($v, [__('admin.All'), 'all', 'All'])));
                if (!$vals) continue;
                if ($field === 'is_active') {
                    $q->whereIn('is_active', array_map(fn($v) => $v == __('admin.Active') ? 1 : 0, $vals));
                } elseif ($field === 'is_kids') {
                    $q->whereIn('is_kids', array_map(fn($v) => $v == __('admin.Yes') ? 1 : 0, $vals));
                } else {
                    $q->whereIn($field, $vals);
                }
            }
        }

        if ($column === 'is_active') return response()->json([__('admin.Active'), __('admin.Inactive')]);
        if ($column === 'is_kids') return response()->json([__('admin.Yes'), __('admin.No')]);

        $unique = $q->whereNotNull($column)->where($column, '!=', '')
            ->distinct()->pluck($column)->filter()->values()->toArray();
        return response()->json($unique);
    }

    public function save(array $data)
    {
        DB::beginTransaction();
        try {
            $sectionItems = $data['sectionItems'] ?? [];
            unset($data['sectionItems']);
            
            $section = $this->repo->save($data);
            $this->syncSectionItems($section, $sectionItems ?? [], true);

            DB::commit();
            return $section;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(array $data, int $id)
    {
        DB::beginTransaction();
        try {
            $sectionItems = $data['sectionItems'] ?? [];
            unset($data['sectionItems']);
            
            $section = $this->repo->update($data, $id);
            $this->syncSectionItems($section, $sectionItems ?? [], true);

            DB::commit();
            return $section;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function syncSectionItems(HomeSection $section, array $sectionItems, bool $replace = false): void
    {
        if ($replace) {
            $section->items()->delete();
        }
        $payload = [];

        foreach ($sectionItems as $row) {
            if (empty($row['content_type']) || empty($row['content_id'])) {
                continue;
            }
            
            $payload[] = [
                'content_type' => $row['content_type'] ?? '',
                'content_id'   => $row['content_id'] ?? '',
                'sort_order'   => $row['sort_order'] ?? 0,
            ];
        }

        if (empty($sectionItems)) {
            $section->items()->delete();
        }

        if (!empty($payload)) {
            $section->items()->createMany($payload);
        }
    }

    public function deleteById(int $id)
    {
        DB::beginTransaction();
        try {
            $section = HomeSection::findOrFail($id);
            $section->items()->delete();
            $deleted = $this->repo->delete($id);
            DB::commit();
            return $deleted;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

