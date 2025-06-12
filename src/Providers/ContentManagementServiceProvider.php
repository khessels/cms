<?php

namespace khessels\cms\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use khessels\cms\Middleware\Language;

class ContentManagementServiceProvider extends ServiceProvider
{
    public function boot( Router $router)
    {
        // Optional: Register routes, views, etc.
        $router->aliasMiddleware('language', Language::class);

        // Load routes
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'package-views');

        // Publish config
        $this->publishes([
            __DIR__.'/../../config/config.php' => config_path('cms.php')
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
