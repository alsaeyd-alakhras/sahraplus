<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Download;

class DownloadsController extends Controller
{
    // GET /api/v1/downloads
    public function index(Request $request)
    {
        return Download::where('user_id',$request->user()->id)
            ->latest()
            ->paginate(20);
    }

    // GET /api/v1/downloads/{id}
    public function show(Download $download)
    {
        // حماية: ما يقدر يجيب تحميل غيره
        $this->authorize('view', $download);
        return $download;
    }
}
