<?php


namespace App\Http\Controllers;
use App\Services\Home\HomeService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function homeWeb(Request $request)
    {
        $profile = $request->get('profile'); // optional
        return app(HomeService::class)->buildHome(
            platform: 'web',
            profile: $profile
        );
    }

    public function homeApi(Request $request)
    {
        $profile = $request->user()?->activeProfile; // حسب نظامك
        return response()->json(
            app(HomeService::class)->buildHome(
                platform: 'mobile',
                profile: $profile
            )
        );
    }
}
