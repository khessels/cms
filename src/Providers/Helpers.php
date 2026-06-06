<?php

namespace Khessels\Cms\Providers;
use Khessels\Cms\Controllers\ContentController;

if (! function_exists('c')) {
    /**
     * Define your package's global helper logic here.
     */
    function c(string $key, string $default, $langCode = null): string
    {
        return ContentController::translate_helper( $key, $default, $langCode);
        //return "Package processed: " . $key;
    }
}
