<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class FrontController extends Controller
{
    public function index()
    {
        return view('site.index');
    }

    public function movie($id)
    {
        $movie = Movie::findOrFail($id);
        return view('site.movie', compact('movie'));
    }


    public function settings()
    {
        $user = Auth::user();
        $user = User::findOrFail($user->id)->with('sessions')->first();
        return view('site.settings', compact('user'));
    }

    public function updatePersonalInfo(Request $request)
    {
        $user = User::findOrFail(Auth::user()->id);
        $user->update($request->all());
        return back()->with('success', __('site.personal_info_updated_successfully'));
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required',
            'confirm_password' => 'required|same:new_password',
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => __('site.password_does_not_match')]);
        }
        $user = User::findOrFail(Auth::user()->id);

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', __('site.password_changed_successfully'));
    }

}
