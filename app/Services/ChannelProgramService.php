<?php

namespace App\Services;

use App\Repositories\ChannelProgramRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ChannelProgramService
{
    public function __construct(private ChannelProgramRepository $repo) {}

    public function datatableIndex(Request $request)
    {
        $q = $this->repo->getQuery()
            ->with('channel:id,name_ar,name_en');

        return DataTables::of($q)
            ->addIndexColumn()
            ->addColumn('channel', fn($p) => $p->channel?->name_ar ?? '-')
            ->addColumn('genre', fn($p) => $p->genre ? __('admin.' . $p->genre) : '-')
            ->addColumn('start_time', fn($p) => $p->start_time?->format('Y-m-d H:i') ?? '-')
            ->addColumn('end_time', fn($p) => $p->end_time?->format('Y-m-d H:i') ?? '-')
            ->addColumn('duration', function ($p) {
                return $p->duration_minutes . ' ' . __('admin.minutes');
            })
            ->addColumn('is_live', fn($p) => $p->is_live ? __('admin.Yes') : __('admin.No'))
            ->addColumn('is_repeat', fn($p) => $p->is_repeat ? __('admin.Yes') : __('admin.No'))
            ->addColumn('edit', fn($p) => $p->id)
            ->filter(function ($query) use ($request) {
                // Apply column filters first
                if ($request->column_filters) {
                    foreach ($request->column_filters as $field => $values) {
                        $vals = array_values(array_filter((array)$values, fn($v) => !in_array($v, ['الكل', 'all', 'All'])));
                        if (!$vals) continue;

                        if ($field === 'channel_id') {
                            $query->whereIn('channel_id', $vals);
                        } elseif ($field === 'genre') {
                            $query->whereIn('genre', $vals);
                        } elseif ($field === 'is_live') {
                            $map = [
                                __('admin.Yes') => 1,
                                'نعم' => 1,
                                '1' => 1,
                                1 => 1,
                                true => 1,
                                __('admin.No') => 0,
                                'لا' => 0,
                                '0' => 0,
                                0 => 0,
                                false => 0
                            ];
                            $query->whereIn('is_live', array_map(fn($v) => $map[$v] ?? $v, $vals));
                        } elseif ($field === 'is_repeat') {
                            $map = [
                                __('admin.Yes') => 1,
                                'نعم' => 1,
                                '1' => 1,
                                1 => 1,
                                true => 1,
                                __('admin.No') => 0,
                                'لا' => 0,
                                '0' => 0,
                                0 => 0,
                                false => 0
                            ];
                            $query->whereIn('is_repeat', array_map(fn($v) => $map[$v] ?? $v, $vals));
                        } else {
                            $query->whereIn($field, $vals);
                        }
                    }
                }

                // Then apply search
                if ($search = $request->get('search')['value'] ?? null) {
                    $query->where(function ($q) use ($search) {
                        $q->where('title_ar', 'like', "%{$search}%")
                            ->orWhere('title_en', 'like', "%{$search}%")
                            ->orWhere('description_ar', 'like', "%{$search}%")
                            ->orWhere('description_en', 'like', "%{$search}%")
                            ->orWhere('genre', 'like', "%{$search}%");
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

                if ($field === 'is_live') {
                    $map = ['نعم' => 1, '1' => 1, 1 => 1, true => 1, 'لا' => 0, '0' => 0, 0 => 0, false => 0];
                    $q->whereIn('is_live', array_map(fn($v) => $map[$v] ?? $v, $vals));
                } elseif ($field === 'is_repeat') {
                    $map = ['نعم' => 1, '1' => 1, 1 => 1, true => 1, 'لا' => 0, '0' => 0, 0 => 0, false => 0];
                    $q->whereIn('is_repeat', array_map(fn($v) => $map[$v] ?? $v, $vals));
                } else {
                    $q->whereIn($field, $vals);
                }
            }
        }

        if ($column === 'is_live') {
            return response()->json([
                ['value' => __('admin.Yes'), 'label' => __('admin.Yes')],
                ['value' => __('admin.No'), 'label' => __('admin.No')]
            ]);
        }
        if ($column === 'is_repeat') {
            return response()->json([
                ['value' => __('admin.Yes'), 'label' => __('admin.Yes')],
                ['value' => __('admin.No'), 'label' => __('admin.No')]
            ]);
        }
        if ($column === 'channel_id') {
            $channels = \App\Models\LiveTvChannel::active()->ordered()->get(['id', 'name_ar']);
            return response()->json($channels->map(fn($c) => ['value' => $c->name_ar, 'label' => $c->name_ar, 'id' => $c->id]));
        }
        if ($column === 'genre') {
            $genres = ['news', 'sports', 'drama', 'documentary', 'entertainment', 'kids', 'religious', 'educational', 'others'];
            return response()->json(array_map(fn($g) => ['value' => $g, 'label' => __('admin.' . $g)], $genres));
        }

        $unique = $q->whereNotNull($column)->where($column, '!=', '')
            ->distinct()->pluck($column)->filter()->values();
        return response()->json($unique->map(fn($v) => ['value' => $v, 'label' => $v]));
    }

    public function checkTimeConflict(int $channelId, string $startTime, string $endTime, int $excludeId = null): bool
    {
        $query = $this->repo->getQuery()
            ->where('channel_id', $channelId)
            ->where(function ($q) use ($startTime, $endTime) {
                $q->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($q2) use ($startTime, $endTime) {
                        $q2->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    public function save(array $data)
    {
        DB::beginTransaction();
        try {
            // Calculate duration
            $start = new \DateTime($data['start_time']);
            $end = new \DateTime($data['end_time']);
            $data['duration_minutes'] = ($end->getTimestamp() - $start->getTimestamp()) / 60;

            // Handle poster upload
            if (isset($data['poster_url_out']) && $data['poster_url_out'] !== null && $data['poster_url_out'] !== '') {
                $data['poster_url'] = $data['poster_url_out'];
                unset($data['poster_url_out']);
            } elseif (!isset($data['poster_url']) || empty($data['poster_url'])) {
                $data['poster_url'] = null;
            }

            // Clean up any remaining file inputs
            unset($data['poster_url_out']);

            $program = $this->repo->save($data);
            DB::commit();
            return $program;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(array $data, int $id)
    {
        DB::beginTransaction();
        try {
            $program = $this->repo->getById($id);

            // Calculate duration
            $start = new \DateTime($data['start_time']);
            $end = new \DateTime($data['end_time']);
            $data['duration_minutes'] = ($end->getTimestamp() - $start->getTimestamp()) / 60;

            // Handle poster upload
            if (isset($data['poster_url_out']) && $data['poster_url_out'] !== null && $data['poster_url_out'] !== '') {
                $data['poster_url'] = $data['poster_url_out'];
                unset($data['poster_url_out']);
            } elseif (!isset($data['poster_url'])) {
                $data['poster_url'] = $program->poster_url;
            }

            // Clean up any remaining file inputs
            unset($data['poster_url_out']);

            $program = $this->repo->update($data, $id);
            DB::commit();
            return $program;
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
        $q = $this->repo->getQuery()
            ->with('channel:id,name_ar');

        // Apply same filters as datatable
        if ($request->column_filters) {
            foreach ($request->column_filters as $field => $values) {
                $vals = array_values(array_filter((array)$values, fn($v) => !in_array($v, ['الكل', 'all', 'All'])));
                if (!$vals) continue;

                if ($field === 'is_live') {
                    $map = ['نعم' => 1, '1' => 1, 1 => 1, true => 1, 'لا' => 0, '0' => 0, 0 => 0, false => 0];
                    $q->whereIn('is_live', array_map(fn($v) => $map[$v] ?? $v, $vals));
                } elseif ($field === 'is_repeat') {
                    $map = ['نعم' => 1, '1' => 1, 1 => 1, true => 1, 'لا' => 0, '0' => 0, 0 => 0, false => 0];
                    $q->whereIn('is_repeat', array_map(fn($v) => $map[$v] ?? $v, $vals));
                } else {
                    $q->whereIn($field, $vals);
                }
            }
        }

        $programs = $q->orderBy('start_time', 'asc')->orderBy('id', 'desc')->get();

        $export = new \App\Exports\ModelExport(
            $programs,
            ['#', 'العنوان بالعربية', 'العنوان بالإنجليزية', 'القناة', 'النوع', 'البداية', 'النهاية', 'المدة', 'مباشر', 'إعادة'],
            function ($prog, $index) {
                return [
                    $index + 1,
                    $prog->title_ar,
                    $prog->title_en ?? '-',
                    $prog->channel?->name_ar ?? '-',
                    $prog->genre ? __('admin.' . $prog->genre) : '-',
                    $prog->start_time->format('Y-m-d H:i'),
                    $prog->end_time->format('Y-m-d H:i'),
                    $prog->duration_minutes . ' دقيقة',
                    $prog->is_live ? 'نعم' : 'لا',
                    $prog->is_repeat ? 'نعم' : 'لا'
                ];
            }
        );

        return \Maatwebsite\Excel\Facades\Excel::download($export, 'channel-programs-' . now()->format('Y-m-d') . '.xlsx');
    }
}
