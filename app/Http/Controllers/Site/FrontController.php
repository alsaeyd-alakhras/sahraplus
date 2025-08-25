<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FrontController extends Controller
{
    public function index()
    {
        return view('site.index');
    }

    public function settings()
    {
        return view('site.settings');
    }

    public function profileStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'avatar_url' => 'required|string',
            'language' => 'nullable|string|max:2',
            'is_child_profile' => 'nullable|boolean',
        ]);
        $user = User::find(Auth::guard('web')->user()->id);
        $profile = $user->profiles()->create([
            'user_id' => $user->id,
            'name' => $request->name,
            'avatar_url' => $request->avatar_url,
            'is_default' => false,
            'is_child_profile' => $request->is_child_profile,
            'pin_code' => null,
            'language' => $request->language ?? 'ar',
            'is_active' => true
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Profile created successfully',
            'profile' => $profile
        ]);
    }
}
