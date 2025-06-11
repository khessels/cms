<?php

namespace Khessels\cms\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Number;

class BladeDirectivesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::share( 'app_url', config('app.url') );
        View::share( 'language', app()->getLocale() );
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

        Blade::directive('c', function ( string $expression) {
            return "<?php echo khessels\ContentManagement\Controllers\ContentController::translate( $expression); ?>";
        });
    }
}
