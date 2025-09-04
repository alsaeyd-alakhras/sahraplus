<?php

namespace App\Services;

use App\Repositories\MovieRepository;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class MovieService
{
    public function __construct(private MovieRepository $repo) {}

    public function datatableIndex(Request $request)
    {
        $q = $this->repo->getQuery();

        // فلاتر الأعمدة (حسب نمطك)
        if ($request->column_filters) {
            foreach ($request->column_filters as $field => $values) {
                $vals = array_values(array_filter((array)$values, fn($v)=>!in_array($v,['الكل','all','All'])));
                if (!$vals) continue;
                if ($field === 'status') {
                    $q->whereIn('status', $vals);
                } elseif ($field === 'is_featured') {
                    $map = ['مميز'=>1,'1'=>1,1=>1,true=>1, 'غير مميز'=>0,'0'=>0,0=>0,false=>0];
                    $q->whereIn('is_featured', array_map(fn($v)=>$map[$v] ?? $v, $vals));
                } else {
                    $q->whereIn($field, $vals);
                }
            }
        }

        return DataTables::of($q)
            ->addIndexColumn()
            ->addColumn('is_featured', fn($m)=> $m->is_featured ? 'مميز' : 'غير مميز')
            ->addColumn('status_label', fn($m)=> match($m->status){'published'=>'منشور','archived'=>'مؤرشف',default=>'مسودة'})
            ->addColumn('edit', fn($m)=> $m->id)
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
                if ($field === 'is_featured') {
                    $map = ['مميز'=>1,'1'=>1,1=>1,true=>1, 'غير مميز'=>0,'0'=>0,0=>0,false=>0];
                    $q->whereIn('is_featured', array_map(fn($v)=>$map[$v] ?? $v, $vals));
                } else {
                    $q->whereIn($field, $vals);
                }
            }
        }

        if ($column === 'status') {
            return response()->json(['draft'=>'مسودة','published'=>'منشور','archived'=>'مؤرشف']);
        }
        if ($column === 'is_featured') {
            return response()->json(['مميز','غير مميز']);
        }

        $unique = $q->whereNotNull($column)->where($column,'!=','')
            ->distinct()->pluck($column)->filter()->values()->toArray();
        return response()->json($unique);
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

            $movie = $this->repo->save($data);
            DB::commit();
            return $movie;
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error',$e->getMessage());
        }
    }

    public function update(array $data, int $id)
    {
        DB::beginTransaction();
        try {
            $movie = $this->repo->getById($id);

            // رفع الصور
            if($data['poster_url_out'] && $data['poster_url_out'] == "" && $data['poster_url_out'] == null){
                $data['poster_url'] = $data['poster_url_out'];
            }else{
                $data['poster_url'] = $data['poster_url'] ?? $movie->poster_url;
            }
            if($data['backdrop_url_out'] && $data['backdrop_url_out'] == "" && $data['backdrop_url_out'] == null){
                $data['backdrop_url'] = $data['backdrop_url_out'];
            }else{
                $data['backdrop_url'] = $data['backdrop_url'] ?? $movie->backdrop_url;
            }

            $data['slug'] = Str::slug($data['title_en'] ?? $data['title_ar']);

            $movie = $this->repo->update($data,$id);

            DB::commit();
            return $movie;
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
