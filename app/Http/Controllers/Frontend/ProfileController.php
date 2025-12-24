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

    public function verifyPin(Request $request, User $user)
    {
        $request->validate([
            'profile_id' => 'nullable|exists:user_profiles,id',
            'pin_code' => 'required|string|max:6',
        ]);

    
        if ($user->pin_code == $request->pin_code) {
            return response()->json(['valid' => true, 'message' => 'تم التحقق بنجاح']);
        }

        return response()->json(['valid' => false, 'message' => 'رمز PIN غير صحيح'], 403);
    }

    public function resetPin(Request $request, User $user)
    {
        $request->validate([
            // 'password' => 'required|string',
            'profile_id' => 'nullable|exists:user_profiles,id',
            'new_pin' => 'required|string|max:6',
        ]);

        // // Check Password
        // if(!Hash::check($request->password,$user->password)){
        //     return response()->json(['message' => 'كلمة السر غير صحيحة'], 401);
        // }

        $profile = null;
        if($request->profile_id){
            $profile = UserProfile::findOrFail($request->profile_id);
            if ($profile->user_id !== Auth::id() || !$profile->is_child_profile) {
                return response()->json(['message' => 'غير مسموح'], 403);
            }
        }

        $user->update([
            'pin_code' => $request->new_pin,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'تم تعيين PIN جديد بنجاح',
            'pin_code' => $request->new_pin
        ]);
    }

    public function createPin(Request $request, User $user)
    {
        $request->validate([
            // 'password' => 'required|string',
            'new_pin' => 'required|string|max:6',
            'profile_id' => 'nullable|exists:user_profiles,id',
        ]);

        // // Check Password
        // if(!Hash::check($request->password,$user->password)){
        //     return response()->json(['message' => 'كلمة السر غير صحيحة'], 401);
        // }

        $profile = null;
        if($request->profile_id){
            $profile = UserProfile::findOrFail($request->profile_id);
            if ($profile->user_id !== Auth::id() || !$profile->is_child_profile) {
                return response()->json(['message' => 'غير مسموح'], 403);
            }
        }

        $user->update([
            'pin_code' => $request->new_pin,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'تم تعيين PIN جديد بنجاح',
            'pin_code' => $request->new_pin
        ]);
    }



    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'avatar_url' => 'nullable|string',
            'language' => 'nullable|string|max:2',
            // 'is_child_profile' => 'nullable|boolean',
        ]);

        $user = User::with(['profiles', 'sessions'])->find(Auth::id());
        $profile = $user->profiles()->create([
            'user_id' => $user->id,
            'name' => $request->name,
            'avatar_url' => $request->avatar_url ?? null,
            'is_default' => false,
            'is_child_profile' => $request->is_child_profile,
            'language' => $request->language ?? 'ar',
            'is_active' => true
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Profile created successfully',
            'profile' => $profile
        ]);
    }

    public function update(Request $request , UserProfile $profile)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'avatar_url' => 'nullable|string',
            'language' => 'nullable|string|max:2',
            // 'is_child_profile' => 'nullable|boolean',
        ]);

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
        $profile->update([
            'name' => $request->name,
            'avatar_url' => $request->avatar_url,
            'is_default' => false,
            'is_child_profile' => $request->is_child_profile,
            'language' => $request->language ?? 'ar',
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully',
            'profile' => $profile
        ]);
    }

    public function destroy(UserProfile $profile)
    {
        if ($profile->user_id !== Auth::user()->id) {
            abort(403);
        }

        $profile->delete();

        return response()->json(['message' => 'deleted']);
    }
}
