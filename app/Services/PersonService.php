<?php

namespace App\Services;

use App\Repositories\PersonRepository;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class PersonService
{
    public function __construct(private PersonRepository $repo) {}

    public function datatableIndex(Request $request)
    {
        $q = $this->repo->getQuery();

        if ($request->column_filters) {
            foreach ($request->column_filters as $field => $values) {
                $vals = array_values(array_filter((array)$values, fn($v)=>!in_array($v,['الكل','all','All'])));
                if (!$vals) continue;

                if ($field === 'is_active') {
                    $map = ['نشط'=>1,'1'=>1,1=>1,true=>1, 'غير نشط'=>0,'0'=>0,0=>0,false=>0];
                    $q->whereIn('is_active', array_map(fn($v)=>$map[$v] ?? $v, $vals));
                } elseif ($field === 'gender') {
                    $q->whereIn('gender', $vals);
                } else {
                    $q->whereIn($field, $vals);
                }
            }
        }

        return DataTables::of($q)
            ->addIndexColumn()
            ->addColumn('is_active', fn($p)=> $p->is_active ? 'نشط' : 'غير نشط')
            ->addColumn('edit', fn($p)=> $p->id)
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

                if ($field === 'is_active') {
                    $map = ['نشط'=>1,'1'=>1,1=>1,true=>1, 'غير نشط'=>0,'0'=>0,0=>0,false=>0];
                    $q->whereIn('is_active', array_map(fn($v)=>$map[$v] ?? $v, $vals));
                } else {
                    $q->whereIn($field, $vals);
                }
            }
        }

        if ($column === 'is_active') {
            return response()->json(['نشط','غير نشط']);
        }
        if ($column === 'gender') {
            return response()->json(['male'=>'ذكر','female'=>'أنثى']);
        }

        $unique = $q->whereNotNull($column)->where($column,'!=','')
            ->distinct()->pluck($column)->filter()->values()->toArray();

        return response()->json($unique);
    }

    public function getById(int $id)
    {
        return $this->repo->getById($id);
    }

    public function save(array $data)
    {
        DB::beginTransaction();
        try {
            $data = $this->handleUploads($data);
            // known_for: اسمح بإرسالها كنص مفصول بفواصل أيضاً
            if (isset($data['known_for']) && is_string($data['known_for'])) {
                $data['known_for'] = array_values(array_filter(array_map('trim', explode(',', $data['known_for']))));
            }
            $p = $this->repo->save($data);
            DB::commit();
            return $p;
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
            $oldPhoto = $old?->photo_url;

            $data = $this->handleUploads($data, $oldPhoto);

            if (isset($data['known_for']) && is_string($data['known_for'])) {
                $data['known_for'] = array_values(array_filter(array_map('trim', explode(',', $data['known_for']))));
            }

            $p = $this->repo->update($data,$id);
            DB::commit();
            return $p;
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error',$e->getMessage());
        }
    }

    public function deleteById(int $id)
    {
        DB::beginTransaction();
        try {
            $p = $this->repo->getById($id);
            if ($p?->photo_url) Storage::disk('public')->delete($p->photo_url);
            $deleted = $this->repo->delete($id);
            DB::commit();
            return $deleted;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function handleUploads(array $data, ?string $oldPhoto=null): array
    {
        if (($data['photoUpload'] ?? null) instanceof UploadedFile) {
            if ($oldPhoto) Storage::disk('public')->delete($oldPhoto);
            $data['photo_url'] = $data['photoUpload']->store('people/photos','public');
        }
        return $data;
    }
}
