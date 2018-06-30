<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;

class PagesController extends Controller
{
    public function root()
    {
        return view('pages.root');
    }

    public function show()
    {
        return view();
    }

}
