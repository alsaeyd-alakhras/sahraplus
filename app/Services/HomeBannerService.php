<?php

namespace App\Services;

use App\Repositories\HomeBannerRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class HomeBannerService
{
    public function __construct(private HomeBannerRepository $repo) {}

    public function datatableIndex(Request $request)
    {
        $q = $this->repo->getQuery()->with(['movie:id,title_ar,title_en', 'series:id,title_ar,title_en']);

        if ($request->column_filters) {
            foreach ($request->column_filters as $field => $values) {
                $vals = array_values(array_filter((array)$values, fn($v)=>!in_array($v,['الكل','all','All'])));
                if (!$vals) continue;
                
                if ($field === 'is_active') {
                    $activeLabel = __('admin.Active');
                    $q->whereIn('is_active', array_map(fn($v)=> $v==$activeLabel?1:0, $vals));
                } elseif ($field === 'is_kids') {
                    $yesLabel = __('admin.Yes');
                    $q->whereIn('is_kids', array_map(fn($v)=> $v==$yesLabel?1:0, $vals));
                } else {
                    $q->whereIn($field, $vals);
                }
            }
        }

        return DataTables::of($q)
            ->addIndexColumn()
            ->addColumn('content_title', function($banner) {
                return $banner->content_title;
            })
            ->addColumn('content_type_label', function($banner) {
                return $banner->content_type === 'movie' ? __('admin.MovieSingular') : __('admin.SeriesSingular');
            })
            ->addColumn('placement_label', function($banner) {
                return $banner->placement === 'mobile_banner' ? __('admin.MobileBanner') : __('admin.FrontendSlider');
            })
            ->addColumn('is_kids_label', fn($b)=> $b->is_kids ? __('admin.Yes') : __('admin.No'))
            ->addColumn('is_active_label', fn($b)=> $b->is_active ? __('admin.Active') : __('admin.Inactive'))
            ->addColumn('edit', fn($b)=> $b->id)
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

        if ($column==='is_active') return response()->json([__('admin.Active'), __('admin.Inactive')]);
        if ($column==='is_kids') return response()->json([__('admin.Yes'), __('admin.No')]);
        if ($column==='content_type') return response()->json(['movie','series']);
        if ($column==='placement') return response()->json(['mobile_banner','frontend_slider']);

        $unique = $q->whereNotNull($column)->where($column,'!=','')
                    ->distinct()->pluck($column)->filter()->values()->toArray();
        return response()->json($unique);
    }

    public function save(array $data)
    {
        DB::beginTransaction();
        try {
            // تعيين sort_order افتراضياً إذا لم يُرسل
            if (!isset($data['sort_order']) || $data['sort_order'] === null) {
                $data['sort_order'] = 0;
            }

            $banner = $this->repo->save($data);
            DB::commit();
            return $banner;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(array $data, int $id)
    {
        DB::beginTransaction();
        try {
            $banner = $this->repo->update($data,$id);
            DB::commit();
            return $banner;
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

