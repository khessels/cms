<?php

namespace khessels\cms\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
//use App\Traits\Content;
use Illuminate\Support\Facades\Session;
use Illuminate\Routing\Controller;

class LanguagesController extends Controller
{
    public function languageSwitch(Request $request)
    {
        $language = $request->input('language');
        Session::put('language', $language);
        return redirect()->back()->with('language_switched' , $language);
    }
}
