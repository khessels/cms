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
        $router->aliasMiddleware('language', Language::class);

        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'package-views');

        // Publish config
        $this->publishes([
            __DIR__ . '/../../config/config.php' => config_path('cms.php'),
            __DIR__ . '/../../resources/views/layouts' => resource_path('views') . '/layouts',
            __DIR__ . '/../../resources/views/partials' => resource_path('views') . '/partials',
            __DIR__ . '/../../resources/views/templates' => resource_path('views') . '/templates',
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
