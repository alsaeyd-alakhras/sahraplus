<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;

class FrontController extends Controller
{
    public function index()
    {
        return view('site.index');
    }
}
