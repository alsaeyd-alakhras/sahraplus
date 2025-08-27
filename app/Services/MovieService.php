<?php

namespace App\Services;

use App\Repositories\MovieRepository;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
            // رفع ملفات اختيارية
            $data = $this->handleUploads($data);
            // من أنشأ؟
            $data['created_by'] = $data['created_by'] ?? optional(Auth::guard('admin')->user())->id;

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
            $oldPoster   = $movie->poster_url;
            $oldBackdrop = $movie->backdrop_url;

            $data = $this->handleUploads($data, $oldPoster, $oldBackdrop);

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
            $m = $this->repo->getById($id);
            if ($m?->poster_url)   Storage::disk('public')->delete($m->poster_url);
            if ($m?->backdrop_url) Storage::disk('public')->delete($m->backdrop_url);
            $deleted = $this->repo->delete($id);
            DB::commit();
            return $deleted;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function handleUploads(array $data, ?string $oldPoster=null, ?string $oldBackdrop=null): array
    {
        if (($data['posterUpload'] ?? null) instanceof UploadedFile) {
            if ($oldPoster) Storage::disk('public')->delete($oldPoster);
            $data['poster_url'] = $data['posterUpload']->store('movies/posters','public');
        }
        if (($data['backdropUpload'] ?? null) instanceof UploadedFile) {
            if ($oldBackdrop) Storage::disk('public')->delete($oldBackdrop);
            $data['backdrop_url'] = $data['backdropUpload']->store('movies/backdrops','public');
        }
        return $data;
    }
}
