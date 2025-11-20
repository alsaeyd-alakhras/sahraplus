<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        return response()->json([
            'profiles' => User::with(['profiles', 'sessions'])->find(Auth::id())->profiles
        ]);
    }

    public function verifyPin(Request $request, UserProfile $profile)
    {
        $request->validate([
            'pin_code' => 'required|string|max:6',
        ]);

        if ($profile->is_child_profile) {
            // تحقق من PIN
            if ($profile->pin_code === $request->pin_code) {
                return response()->json(['valid' => true]);
            }
            return response()->json(['valid' => false], 403);
        }

        // للبروفايلات العادية (لو طلبوا التحقق بكلمة سر الـ User)
        if ($request->has('password')) {
            if (Hash::check($request->password, Auth::guard('web')->user()->password)) {
                return response()->json(['valid' => true]);
            }
            return response()->json(['valid' => false], 403);
        }

        return response()->json(['valid' => false], 400);
    }

    public function resetPin(Request $request, UserProfile $profile)
    {
        $request->validate([
            'password' => 'required|string',
            'new_pin' => 'required|string|max:6',
        ]);

        if (
            $profile->user_id !== Auth::id() ||
            !$profile->is_child_profile
        ) {
            return response()->json(['message' => 'غير مسموح'], 403);
        }

        if (!Hash::check($request->password, Auth::user()->password)) {
            return response()->json(['message' => 'كلمة المرور غير صحيحة'], 403);
        }

        $profile->update([
            'pin_code' => $request->new_pin,
        ]);

        return response()->json(['message' => 'تم تعيين PIN جديد بنجاح']);
    }



    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'avatar_url' => 'required|string',
            'language' => 'nullable|string|max:2',
            // 'is_child_profile' => 'nullable|boolean',
            'pin_code' => 'nullable|string|max:6',
        ]);

        if (
            config('settings.require_pin_for_children') &&
            $request->is_child_profile &&
            empty($request->pin_code)
        ) {
            return response()->json(['message' => 'يجب إدخال PIN لملفات الأطفال'], 422);
        }


        $user = User::with(['profiles', 'sessions'])->find(Auth::id());
        $profile = $user->profiles()->create([
            'user_id' => $user->id,
            'name' => $request->name,
            'avatar_url' => $request->avatar_url,
            'is_default' => false,
            'is_child_profile' => $request->is_child_profile,
            'pin_code' => $request->pin_code ?? null,
            'language' => $request->language ?? 'ar',
            'is_active' => true
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Profile created successfully',
            'profile' => $profile
        ]);
    }

    public function update(UserProfile $profile, Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'avatar_url' => 'required|string',
            'language' => 'nullable|string|max:2',
            // 'is_child_profile' => 'nullable|boolean',
            'pin_code' => 'nullable|string|max:6',
        ]);
        if (config('settings.require_pin_for_children') && $request->is_child_profile && empty($request->pin_code)) {
            return response()->json(['message' => 'ملفات الأطفال يجب أن تحتوي على PIN'], 422);
        }

        // منع تعديل الأطفال بدون تحقق
        if ($profile->is_child_profile) {
            $verified = false;

            if ($request->has('pin_code') && $profile->pin_code === $request->pin_code) {
                $verified = true;
            }
            if ($request->has('password') && Hash::check($request->password, Auth::guard('web')->user()->password)) {
                $verified = true;
            }

            if (!$verified) {
                return response()->json(['message' => 'التحقق مطلوب لتعديل ملف الطفل'], 403);
            }
        }
        $profile = $profile->update([
            'name' => $request->name,
            'avatar_url' => $request->avatar_url,
            'is_default' => false,
            'is_child_profile' => $request->is_child_profile,
            'language' => $request->language ?? 'ar',
            'pin_code' => $request->pin_code ?? null,
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully',
            'profile' => $profile
        ]);
    }

    public function destroy(UserProfile $profile)
    {
        if ($profile->user_id !== Auth::guard('web')->user()->id) {
            abort(403);
        }

        $profile->delete();

        return response()->json(['message' => 'deleted']);
    }
}
