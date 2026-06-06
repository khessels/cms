<?php

// packages/your-vendor/your-package/src/helpers.php

if (! function_exists('c')) {
    /**
     * Define your package's global helper logic here.
     */
    function c(string $key, string $default, $langCode = null): string
    {
        return Khessels\Cms\Controllers\ContentController::translate_helper( $key, $default, $langCode);
        //return "Package processed: " . $key;
    }
}
