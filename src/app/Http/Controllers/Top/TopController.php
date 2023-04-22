<?php

namespace App\Http\Controllers\Top;

use App\Http\Controllers\Controller;

class TopController extends Controller
{
    public function __invoke()
    {
        //Topページ表示
        return view('page.top.index');
    }
}