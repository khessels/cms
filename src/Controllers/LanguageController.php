<?php

namespace khessels\cms\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Routing\Controller;

class LanguageController extends Controller
{
    public function update(Request $request)
    {
        $language = $request->input( 'language');
        Session::put( 'language', $language);
        return redirect()->back();
    }
}
