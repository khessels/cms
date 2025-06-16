<?php

namespace khessels\cms\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Routing\Controller;

class LanguagesController extends Controller
{
    public function languageSwitch(Request $request)
    {
        $language = $request->input( 'language');
        Session::put( 'language', $language);
        return redirect()->back();
    }
}
