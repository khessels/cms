<?php

namespace Khessels\Cms\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LanguageController extends CMSController
{
    public function update(Request $request)
    {
        $language = $request->input('language');
        Session::put('language', $language);
        return redirect()->back();
    }
}
