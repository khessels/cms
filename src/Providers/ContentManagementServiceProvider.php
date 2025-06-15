<?php

namespace khessels\cms\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use khessels\cms\Middleware\Language;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Http\Kernel;

class ContentManagementServiceProvider extends ServiceProvider
{
    public function boot( Router $router, Kernel $kernel)
    {
        // Optional: Register routes, views, etc.


        //$router->aliasMiddleware('language', Language::class);
        // $router->pushMiddlewareToGroup('web', Language::class);
        // Register the middleware

        //$kernel->prependMiddlewareToGroup('web', Language::class);
        // Load routes
        //$this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
        $router->aliasMiddleware('language', Language::class);
        $router->pushMiddlewareToGroup('web', 'language');
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
        // $router->routeMiddleware([
        //     Route::middleware( [ 'web']) // Replace 'your-middleware' with the desired middleware
        //         ->group(function () {
        //             $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
        //         }),
        // ]);
        // $router->routes( function () {
        //     Route::middleware( [ "web"]) // Replace 'your-middleware' with the desired middleware
        //     ->group(function () {
        //        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
        //     });
        // });

        // Load views
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'package-views');

        // Publish config
        $this->publishes([
            __DIR__.'/../../config/config.php' => config_path('cms.php'),
            __DIR__.'/../../partials' => resource_path('views') . '/partials',
        ]);
    }

    public function register()
    {
        // Optional: Bind services, etc.
        $this->app->config['filesystems.disks.resources'] = [
            'driver' => 'local',
            'root' => storage_path('app/resources'),
            'serve' => false,
            'throw' => false
        ];
        $this->app->config['app.available_locales'] = ['en', 'es'];
    }
}
