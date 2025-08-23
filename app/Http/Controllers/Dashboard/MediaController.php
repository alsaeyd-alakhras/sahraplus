<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MediaController extends Controller
{
    public function index()
    {
        $media = Media::latest()->get();
        $request = request();
        if ($request->ajax()) {
            return response()->json($media);
        }
        return view('dashboard.pages.media');
    }

    public function store(Request $request)
    {
        // إذا أرسلت كمصفوفة images[] (متعدد)
        if ($request->hasFile('images')) {
            $request->validate([
                'images'   => 'required|array',
                'images.*' => 'file|image|max:5120', // 5MB مثلاً
            ]);

            $files = $request->file('images');
            $saved = [];

            foreach ($files as $file) {
                $path = $file->store('uploads/media', 'public');

                $media = Media::create([
                    'name'        => $file->getClientOriginalName(),
                    'file_path'   => $path,
                    'mime_type'   => $file->getMimeType(),
                    'size'        => $file->getSize(),
                    'uploader_id' => Auth::id(),
                ]);

                $saved[] = $media;
            }

            return response()->json([
                'status' => 'ok',
                'count'  => count($saved),
                'items'  => $saved,
            ]);
        }

        // دعم الاسم القديم image (مفرد) لتوافق الخلف
        if ($request->hasFile('image')) {
            $request->validate([
                'image' => 'required|file|image|max:5120',
            ]);

            $file = $request->file('image');
            $path = $file->store('uploads/media', 'public');

            $media = Media::create([
                'name'        => $file->getClientOriginalName(),
                'file_path'   => $path,
                'mime_type'   => $file->getMimeType(),
                'size'        => $file->getSize(),
                'uploader_id' => Auth::id(),
            ]);

            return response()->json([
                'status' => 'ok',
                'count'  => 1,
                'items'  => [$media],
            ]);
        }

        // لا يوجد ملفات
        return response()->json([
            'status'  => 'error',
            'message' => 'لم يتم اختيار أي صور.',
        ], 422);
    }

    public function show($id)
    {
        $media = Media::findOrFail($id);
        return response()->json($media);
    }

    public function edit($id)
    {
        $media = Media::findOrFail($id);
        return response()->json($media);
    }

    public function update(Request $request, $id)
    {
        $media = Media::findOrFail($id);

        $request->validate([
            'name' => 'nullable|string|max:255',
            'alt' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'caption' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $media->update($request->all());

        return response()->json(['message' => 'تم التحديث بنجاح']);
    }


    public function destroy($id)
    {
        $media = Media::findOrFail($id);
        Storage::disk('public')->delete($media->file_path);
        $media->delete();

        return response()->json(['message' => 'تم الحذف']);
    }
}
