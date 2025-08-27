<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\UserAvatar;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAvatarController extends Controller
{
    public function index()
    {
        $user_avatar = UserAvatar::latest()->get();
        $request = request();
        if ($request->ajax()) {
            return response()->json($user_avatar);
        }
        return view('dashboard.pages.user_avatar');
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
                $path = $file->store('uploads/user_avatar', 'public');

                $user_avatar = UserAvatar::create([
                    'name'        => $file->getClientOriginalName(),
                    'image_url'   => $path,
                    'category'    => 'general',
                    'is_default'  => false,
                    'is_active'   => true,
                    'sort_order'  => 0,
                ]);

                $saved[] = $user_avatar;
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
            $path = $file->store('uploads/user_avatar', 'public');

            $user_avatar = UserAvatar::create([
                'name'        => $file->getClientOriginalName(),
                'image_url'   => $path,
                'category'    => 'general',
                'is_default'  => false,
                'is_active'   => true,
                'sort_order'  => 0,
            ]);

            return response()->json([
                'status' => 'ok',
                'count'  => 1,
                'items'  => [$user_avatar],
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
        $user_avatar = UserAvatar::findOrFail($id);
        return response()->json($user_avatar);
    }

    public function edit($id)
    {
        $user_avatar = UserAvatar::findOrFail($id);
        return response()->json($user_avatar);
    }

    public function update(Request $request, $id)
    {
        $user_avatar = UserAvatar::findOrFail($id);

        $request->validate([
            'name' => 'nullable|string|max:255',
            'image_url' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'is_default' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $user_avatar->update($request->all());

        return response()->json(['message' => __('controller.Updated_item_successfully')]);
    }


    public function destroy($id)
    {
        $user_avatar = UserAvatar::findOrFail($id);
        Storage::disk('public')->delete($user_avatar->image_url);
        $user_avatar->delete();

        return response()->json(['message' => __('controller.Deleted_item_successfully')]);
    }
}
