<?php

namespace App\Services;

use App\Repositories\LiveTvChannelRepository;
use App\Jobs\SyncChannelEPG;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class LiveTvChannelService
{
    public function __construct(private LiveTvChannelRepository $repo) {}

    public function datatableIndex(Request $request)
    {
        $q = $this->repo->getQuery()
            ->with('category:id,name_ar,name_en')
            ->withCount('programs');

        return DataTables::of($q)
            ->addIndexColumn()
            ->addColumn('category', fn($c) => $c->category?->name_ar ?? '-')
            ->addColumn('stream_type', function ($c) {
                $types = ['hls' => 'HLS', 'dash' => 'DASH', 'rtmp' => 'RTMP'];
                return $types[$c->stream_type] ?? $c->stream_type;
            })
            ->addColumn('stream_health', function ($c) {
                $statusLabels = [
                    'online' => '<span class="badge bg-success">Online</span>',
                    'offline' => '<span class="badge bg-danger">Offline</span>',
                    'unknown' => '<span class="badge bg-secondary">Unknown</span>',
                ];
                $status = $statusLabels[$c->stream_health_status] ?? $statusLabels['unknown'];
                $lastCheck = $c->stream_health_last_check
                    ? '<small class="text-muted d-block">' . $c->stream_health_last_check->diffForHumans() . '</small>'
                    : '';
                return $status . $lastCheck;
            })
            ->addColumn('is_featured', fn($c) => $c->is_featured ? __('admin.featured') : __('admin.not_featured'))
            ->addColumn('is_active', fn($c) => $c->is_active ? __('admin.active') : __('admin.inactive'))
            ->addColumn('edit', fn($c) => $c->id)
            ->rawColumns(['stream_health'])
            ->filter(function ($query) use ($request) {
                // Apply column filters first
                if ($request->column_filters) {
                    foreach ($request->column_filters as $field => $values) {
                        $vals = array_values(array_filter((array)$values, fn($v) => !in_array($v, ['الكل', 'all', 'All'])));
                        if (!$vals) continue;

                        if ($field === 'is_active') {
                            $map = [
                                __('admin.active') => 1,
                                'نشط' => 1,
                                '1' => 1,
                                1 => 1,
                                true => 1,
                                __('admin.inactive') => 0,
                                'غير نشط' => 0,
                                '0' => 0,
                                0 => 0,
                                false => 0
                            ];
                            $query->whereIn('is_active', array_map(fn($v) => $map[$v] ?? $v, $vals));
                        } elseif ($field === 'is_featured') {
                            $map = [
                                __('admin.featured') => 1,
                                'مميز' => 1,
                                '1' => 1,
                                1 => 1,
                                true => 1,
                                __('admin.not_featured') => 0,
                                'غير مميز' => 0,
                                '0' => 0,
                                0 => 0,
                                false => 0
                            ];
                            $query->whereIn('is_featured', array_map(fn($v) => $map[$v] ?? $v, $vals));
                        } elseif ($field === 'category_id') {
                            $query->whereIn('category_id', $vals);
                        } elseif ($field === 'stream_type') {
                            $query->whereIn('stream_type', $vals);
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
                            ->orWhere('stream_url', 'like', "%{$search}%")
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
                    $map = [
                        __('admin.active') => 1,
                        'نشط' => 1,
                        '1' => 1,
                        1 => 1,
                        true => 1,
                        __('admin.inactive') => 0,
                        'غير نشط' => 0,
                        '0' => 0,
                        0 => 0,
                        false => 0
                    ];
                    $q->whereIn('is_active', array_map(fn($v) => $map[$v] ?? $v, $vals));
                } elseif ($field === 'is_featured') {
                    $map = [
                        __('admin.featured') => 1,
                        'مميز' => 1,
                        '1' => 1,
                        1 => 1,
                        true => 1,
                        __('admin.not_featured') => 0,
                        'غير مميز' => 0,
                        '0' => 0,
                        0 => 0,
                        false => 0
                    ];
                    $q->whereIn('is_featured', array_map(fn($v) => $map[$v] ?? $v, $vals));
                } else {
                    $q->whereIn($field, $vals);
                }
            }
        }

        if ($column === 'is_active') {
            return response()->json([
                ['value' => __('admin.active'), 'label' => __('admin.active')],
                ['value' => __('admin.inactive'), 'label' => __('admin.inactive')]
            ]);
        }
        if ($column === 'is_featured') {
            return response()->json([
                ['value' => __('admin.featured'), 'label' => __('admin.featured')],
                ['value' => __('admin.not_featured'), 'label' => __('admin.not_featured')]
            ]);
        }
        if ($column === 'stream_type') {
            return response()->json([
                ['value' => 'HLS', 'label' => 'HLS'],
                ['value' => 'DASH', 'label' => 'DASH'],
                ['value' => 'RTMP', 'label' => 'RTMP']
            ]);
        }
        if ($column === 'category_id') {
            $categories = \App\Models\LiveTvCategory::active()->ordered()->get(['id', 'name_ar']);
            return response()->json($categories->map(fn($c) => ['value' => $c->name_ar, 'label' => $c->name_ar, 'id' => $c->id]));
        }

        $unique = $q->whereNotNull($column)->where($column, '!=', '')
            ->distinct()->pluck($column)->filter()->values();
        return response()->json($unique->map(fn($v) => ['value' => $v, 'label' => $v]));
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
            // Set default language if not provided or empty
            if (empty($data['language'])) {
                $data['language'] = 'ar';
            }

            // Generate slug
            $nameForSlug = $data['name_en'] ?? $data['name_ar'];
            $data['slug'] = !empty($data['slug']) ? $data['slug'] : $this->uniqueSlug($nameForSlug);

            // Handle logo upload
            if (isset($data['logo_url_out']) && $data['logo_url_out'] !== null && $data['logo_url_out'] !== '') {
                $data['logo_url'] = $data['logo_url_out'];
                unset($data['logo_url_out']);
            } elseif (!isset($data['logo_url']) || empty($data['logo_url'])) {
                $data['logo_url'] = null;
            }

            // Handle poster upload
            if (isset($data['poster_url_out']) && $data['poster_url_out'] !== null && $data['poster_url_out'] !== '') {
                $data['poster_url'] = $data['poster_url_out'];
                unset($data['poster_url_out']);
            } elseif (!isset($data['poster_url']) || empty($data['poster_url'])) {
                $data['poster_url'] = null;
            }

            // Clean up any remaining file inputs
            unset($data['logo_url_out'], $data['poster_url_out']);

            // Set default viewer_count
            $data['viewer_count'] = $data['viewer_count'] ?? 0;

            $channel = $this->repo->save($data);
            DB::commit();

            // Queue EPG sync job to run in background
            if (!empty($channel->epg_id)) {
                Log::info('LiveTvChannelService: Queuing EPG sync for new channel', [
                    'channel_id' => $channel->id,
                    'epg_id' => $channel->epg_id,
                ]);

                // Dispatch to queue (will run immediately if queue driver is 'sync', otherwise in background)
                SyncChannelEPG::dispatch($channel->id, $channel->epg_id);
            }

            return $channel;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(array $data, int $id)
    {
        DB::beginTransaction();
        try {
            $channel = $this->repo->getById($id);

            // Generate slug if provided or names changed
            if (!empty($data['slug'])) {
                $data['slug'] = $this->uniqueSlug($data['slug'], $id);
            } elseif (isset($data['name_en']) || isset($data['name_ar'])) {
                $nameForSlug = $data['name_en'] ?? $data['name_ar'] ?? $channel->name_en ?? $channel->name_ar;
                $data['slug'] = $this->uniqueSlug($nameForSlug, $id);
            }

            // Handle logo upload
            if (isset($data['logo_url_out']) && $data['logo_url_out'] !== null && $data['logo_url_out'] !== '') {
                $data['logo_url'] = $data['logo_url_out'];
                unset($data['logo_url_out']);
            } elseif (!isset($data['logo_url'])) {
                $data['logo_url'] = $channel->logo_url;
            }

            // Handle poster upload
            if (isset($data['poster_url_out']) && $data['poster_url_out'] !== null && $data['poster_url_out'] !== '') {
                $data['poster_url'] = $data['poster_url_out'];
                unset($data['poster_url_out']);
            } elseif (!isset($data['poster_url'])) {
                $data['poster_url'] = $channel->poster_url;
            }

            // Clean up any remaining file inputs
            unset($data['logo_url_out'], $data['poster_url_out']);

            $channel = $this->repo->update($data, $id);
            DB::commit();
            return $channel;
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
            ->with('category:id,name_ar')
            ->withCount('programs');

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

        $channels = $q->orderBy('sort_order', 'asc')->orderBy('id', 'desc')->get();

        $export = new \App\Exports\ModelExport(
            $channels,
            ['#', 'الاسم بالعربية', 'الاسم بالإنجليزية', 'الفئة', 'نوع البث', 'اللغة', 'البلد', 'الترتيب', 'مميز', 'نشط', 'عدد البرامج', 'المشاهدين'],
            function ($ch, $index) {
                $types = ['hls' => 'HLS', 'dash' => 'DASH', 'rtmp' => 'RTMP'];
                return [
                    $index + 1,
                    $ch->name_ar,
                    $ch->name_en,
                    $ch->category?->name_ar ?? '-',
                    $types[$ch->stream_type] ?? $ch->stream_type,
                    $ch->language ?? '-',
                    $ch->country ?? '-',
                    $ch->sort_order,
                    $ch->is_featured ? 'نعم' : 'لا',
                    $ch->is_active ? 'نعم' : 'لا',
                    $ch->programs_count ?? 0,
                    $ch->viewer_count ?? 0
                ];
            }
        );

        return \Maatwebsite\Excel\Facades\Excel::download($export, 'live-tv-channels-' . now()->format('Y-m-d') . '.xlsx');
    }
}
