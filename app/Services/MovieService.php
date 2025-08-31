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

        // التقط العلاقات (ولا ترسلها للـ repo)
        $categoryIds = $data['category_ids'] ?? [];
        $personIds   = $data['person_ids']   ?? [];
        unset($data['category_ids'], $data['person_ids']);

        // الصور (نفضل *_out إن وُجدت وقيمتها غير فارغة)
        if (array_key_exists('poster_url_out', $data) && $data['poster_url_out'] !== null && $data['poster_url_out'] !== '') {
            $data['poster_url'] = $data['poster_url_out'];
        } else {
            $data['poster_url'] = $data['poster_url'] ?? null;
        }

        if (array_key_exists('backdrop_url_out', $data) && $data['backdrop_url_out'] !== null && $data['backdrop_url_out'] !== '') {
            $data['backdrop_url'] = $data['backdrop_url_out'];
        } else {
            $data['backdrop_url'] = $data['backdrop_url'] ?? null;
        }

        unset($data['poster_url_out'], $data['backdrop_url_out']);

        // slug
        $data['slug'] = Str::slug($data['title_en'] ?? $data['title_ar']);

        // إنشاء الفيلم
        $movie = $this->repo->save($data);

        // مزامنة العلاقات (لو فيه اختيارات)
        if (!empty($categoryIds)) {
            $movie->categories()->sync($categoryIds);
        }
        if (!empty($personIds)) {
            $movie->people()->sync($personIds);
        }

        DB::commit();
        return $movie;
    } catch (\Throwable $e) {
        DB::rollBack();
        return back()->with('error', $e->getMessage());
    }
}

public function update(array $data, int $id)
{
    DB::beginTransaction();
    try {
        $movie = $this->repo->getById($id);

        // التقط العلاقات (واحذفها من $data قبل التحديث)
        $hasCategoryIds = array_key_exists('category_ids', $data);
        $hasPersonIds   = array_key_exists('person_ids', $data);

        $categoryIds = $hasCategoryIds ? (array)($data['category_ids'] ?? []) : null;
        $personIds   = $hasPersonIds   ? (array)($data['person_ids']   ?? []) : null;

        unset($data['category_ids'], $data['person_ids']);

        // الصور: نفضل *_out إذا وُجدت وغير فارغة، وإلا نحافظ على القيمة السابقة
        if (array_key_exists('poster_url_out', $data) && $data['poster_url_out'] !== null && $data['poster_url_out'] !== '') {
            $data['poster_url'] = $data['poster_url_out'];
        } else {
            $data['poster_url'] = $data['poster_url'] ?? $movie->poster_url;
        }

        if (array_key_exists('backdrop_url_out', $data) && $data['backdrop_url_out'] !== null && $data['backdrop_url_out'] !== '') {
            $data['backdrop_url'] = $data['backdrop_url_out'];
        } else {
            $data['backdrop_url'] = $data['backdrop_url'] ?? $movie->backdrop_url;
        }

        unset($data['poster_url_out'], $data['backdrop_url_out']);

        // slug
        $data['slug'] = Str::slug($data['title_en'] ?? $data['title_ar']);

        // تحديث بيانات الفيلم
        $movie = $this->repo->update($data, $id);

        // مزامنة العلاقات:
        // - لو المفتاح موجود في الريكوست حتى لو مصفوفة فاضية => sync([]) = تفريغ العلاقات
        // - لو المفتاح غير موجود => لا نلمس العلاقات
        if ($hasCategoryIds) {
            $movie->categories()->sync($categoryIds ?? []);
        }
        if ($hasPersonIds) {
            $movie->people()->sync($personIds ?? []);
        }

        DB::commit();
        return $movie;
    } catch (\Throwable $e) {
        DB::rollBack();
        return back()->with('error', $e->getMessage());
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
