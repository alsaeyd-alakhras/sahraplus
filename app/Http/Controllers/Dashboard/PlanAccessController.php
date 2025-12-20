<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Season;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PlanContentAccessRequest;
use App\Models\Movie;
use App\Models\Category;
use App\Models\PlanContentAccess;
use App\Models\Series;
use App\Services\PlanAccessService;
use App\Models\SubscriptionPlan;

class PlanAccessController extends Controller
{
    protected $subscription_plans;
    public function getContents(Request $request)
    {
        $type = $request->type;
        $locale = app()->getLocale(); // 'ar' أو 'en'

        switch ($type) {
            case 'category':
                $contents = Category::select('id', 'name_ar', 'name_en')->get();
                break;
            case 'movie':
                $contents = Movie::select('id', 'title_ar', 'title_en')->get();
                break;
            case 'series':
                $contents = Series::select('id', 'title_ar', 'title_en')->get();
                break;
            default:
                $contents = collect();
        }

        // تحويل الاسم حسب اللغة
        $contents = $contents->map(function ($item) use ($locale, $type) {
            if ($type === 'category') {
                $item->name = $locale === 'ar' ? $item->name_ar : $item->name_en;
            } else {
                $item->name = $locale === 'ar' ? $item->title_ar : $item->title_en;
            }
            return $item;
        });

        return response()->json($contents);
    }
}
