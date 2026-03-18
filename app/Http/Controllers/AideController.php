<?php

namespace App\Http\Controllers;

class AideController extends Controller
{
    public function __invoke()
    {
        return view('aide.index');
    }
}
