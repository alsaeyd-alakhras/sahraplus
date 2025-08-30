<?php

namespace App\Services;

use App\Repositories\ShortRepository;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ShortService
{
    public function __construct(private ShortRepository $repo) {}

    public function datatableIndex(Request $request)
    {
        $q = $this->repo->getQuery();

        if ($request->column_filters) {
            foreach ($request->column_filters as $field => $values) {
                $vals = array_values(array_filter((array)$values, fn($v)=>!in_array($v,['الكل','all','All'])));
                if (!$vals) continue;

                if ($field === 'is_featured') {
                    $q->whereIn('is_featured', array_map(fn($v)=> $v=='مميز'?1:0, $vals));
                } elseif ($field === 'status') {
                    $q->whereIn('status', $vals);
                } else {
                    $q->whereIn($field, $vals);
                }
            }
        }

        return DataTables::of($q)
            ->addIndexColumn()
            ->addColumn('is_featured', fn($s)=> $s->is_featured ? 'مميز' : 'عادي')
            ->addColumn('status', fn($s)=> $s->status === 'active' ? 'نشط' : 'غير نشط')
            ->addColumn('edit', fn($s)=> $s->id)
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

        if ($column === 'is_featured') {
            return response()->json(['مميز','عادي']);
        }
        if ($column === 'status') {
            return response()->json(['active'=>'نشط','inactive'=>'غير نشط']);
        }

        $unique = $q->whereNotNull($column)->where($column,'!=','')
            ->distinct()->pluck($column)->filter()->values()->toArray();

        return response()->json($unique);
    }

    public function getById(int $id) { return $this->repo->getById($id); }

    public function save(array $data)
    {
        DB::beginTransaction();
        try {
            $data = $this->handleUploads($data);
            $s = $this->repo->save($data);
            DB::commit();
            return $s;
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error',$e->getMessage());
        }
    }

    public function update(array $data, int $id)
    {
        DB::beginTransaction();
        try {
            $old = $this->repo->getById($id);
            $data = $this->handleUploads($data, $old?->poster_path, $old?->video_path);
            $s = $this->repo->update($data,$id);
            DB::commit();
            return $s;
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error',$e->getMessage());
        }
    }

    public function deleteById(int $id)
    {
        DB::beginTransaction();
        try {
            $s = $this->repo->getById($id);
            if ($s?->poster_path) Storage::disk('public')->delete($s->poster_path);
            if ($s?->video_path) Storage::disk('public')->delete($s->video_path);
            $deleted = $this->repo->delete($id);
            DB::commit();
            return $deleted;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function handleUploads(array $data, ?string $oldPoster=null, ?string $oldVideo=null): array
    {
        if (($data['posterUpload'] ?? null) instanceof UploadedFile) {
            if ($oldPoster) Storage::disk('public')->delete($oldPoster);
            $data['poster_path'] = $data['posterUpload']->store('shorts/posters','public');
        }
        if (($data['videoUpload'] ?? null) instanceof UploadedFile) {
            if ($oldVideo) Storage::disk('public')->delete($oldVideo);
            $data['video_path'] = $data['videoUpload']->store('shorts/videos','public');
        }
        return $data;
    }
}
