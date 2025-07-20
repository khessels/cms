<?php

namespace khessels\cms\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Number;
use khessels\cms\Controllers\ContentController;

class BladeDirectivesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        view()->share( 'app_url', config('app.url') );

        // $language = empty( Session::get('language') ) ? app()->getLocale() : Session::get('language');
        // view()->share( 'language', $language );

        if (! app()->runningInConsole()) {
            $pagesCache = Cache::get('pages');
            if( empty( $pagesCache) && config('cms.enabled') ){
                // Retrieve pages from the CMS
                ContentController::_retrievePages();
            }
        }

        Blade::directive('money_old', function ( $amount) {
            return "<?php echo '€' . number_format($amount, 2, ',', '.'); ?>";
        });

        Blade::directive('money', function ( $amount) {
            return "<?php echo Number::currency( $amount, in: 'EUR', locale: App::getLocale()) ?>";
        });

        Blade::directive('number', function ( $number, $default = null) {
            return "<?php echo Number::format( $number,  precision: 0, locale: App::getLocale()); ?>";
        });

        Blade::directive('round', function ( $amount, $precision = 1) {
            return "<?php echo round( $amount, $precision); ?>";
        });
        // $rey = app()->runningInConsole();
//        if (! app()->runningInConsole()) {
        Blade::directive('c', function ( string $expression) {
            return "<?php echo khessels\cms\Controllers\ContentController::translate( $expression); ?>";
        });
//        }

    }
}
