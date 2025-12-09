<?php

namespace App\Services;

use App\Repositories\PaymentsRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class PaymentsService
{
    public function __construct(private PaymentsRepository $repo) {}

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
            ->addColumn('status', function ($row) {
                $statuses = [
                    'pending'    => __('admin.pending'),
                    'processing' => __('admin.processing'),
                    'completed'  => __('admin.completed'),
                    'failed'     => __('admin.failed'),
                    'refunded'   => __('admin.refunded'),
                    'canceled'   => __('admin.canceled'),
                ];

                return $statuses[$row->status] ?? $row->status;
            })
            ->addColumn('subscription_id', function ($row) {
                return $row->subscription->plan ?? '' ? app()->getLocale() == 'ar' ? $row->subscription->plan->name_ar : $row->subscription->plan->name_en : '';
            })->addColumn('user_id', function ($row) {
                return $row->user ? $row->user->first_name . ' ' . $row->user->last_name : '';
            })
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
