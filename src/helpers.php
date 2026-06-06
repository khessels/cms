<?php

// packages/your-vendor/your-package/src/helpers.php
use
if (! function_exists('c')) {
    /**
     * Define your package's global helper logic here.
     */
    function c(string $key, $default, $langCode): string
    {
        return Khessels\Cms\Controllers\ContentController::translate_helper( $key, $default, $langCode);
        //return "Package processed: " . $key;
    }
}
