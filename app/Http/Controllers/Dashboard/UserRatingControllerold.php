<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\UserRating;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRatingRequest;

class UserRatingController extends Controller
{
    public function __construct()
    {
      //  $this->middleware('auth'); // توثيق الويب
    }

    public function index()
    {
        $ratings = UserRating::latest()->paginate(12);
        return view('user_ratings.index', compact('ratings'));
    }

    public function create()
    {
        return view('user_ratings.create');
    }

    public function store(UserRatingRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['status']  = $data['status'] ?? 'approved';

        UserRating::create($data);
        return redirect()->route('user-ratings.index')->with('success', 'تمت الإضافة بنجاح');
    }

    public function edit(UserRating $user_rating)
    {
        abort_unless($user_rating->user_id === auth()->id(), 403);
        return view('user_ratings.edit', compact('user_rating'));
    }

    public function update(UserRatingRequest $request, UserRating $user_rating)
    {
        abort_unless($user_rating->user_id === auth()->id(), 403);
        $user_rating->update($request->validated());
        return redirect()->route('user-ratings.index')->with('success', 'تم التعديل بنجاح');
    }

    public function destroy(UserRating $user_rating)
    {
        abort_unless($user_rating->user_id === auth()->id(), 403);
        $user_rating->delete();
        return redirect()->route('user-ratings.index')->with('success', 'تم الحذف بنجاح');
    }
}