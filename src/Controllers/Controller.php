<?php
namespace khessels\cms\Controllers;

use Illuminate\Support\Facades\Session;

abstract class Controller
{
    public function getFilesRecursive($dir) {
        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    public function alertNotification( $message = 'Success', $class = 'primary', $disappear = 3000){
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
