<?php

namespace App\Http\Controllers;

// use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function __invoke(Request $request)
    {
        Session::put('lang', $request->input('lang'));

        // App::setlocale($request->input('lang'));

        return redirect()->back();
    }
}
