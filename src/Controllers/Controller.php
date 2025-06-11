<?php
namespace khessels\cms\Controllers;

use Illuminate\Support\Facades\Session;

abstract class Controller
{
    public function alertNotification( $message = 'Success', $class = 'success', $disappear = 3000){
        Session::flash('alert-message', $message);
        Session::flash('alert-timeout', $disappear);
        Session::flash('alert-class', 'alert-' . $class);
    }
    public function criticalException($request, $e, $file, $function, $line, $payload = null): void
    {
        if(!empty($e)){
            error_log($e->getMessage());
        }
    }
}
