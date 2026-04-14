<?php

use Illuminate\Support\Facades\Route;
use Khessels\Cms\Controllers\LanguageController;

if(config('cms.enabled')) {
    if( config('cms.route_debug')) error_log('CMS enabled');
    if ( sizeof( explode( ',', config('cms.route_middleware_encapsulation'))) > 0) // default web, language
    {
        if( config('cms.route_debug')) error_log('CMS route_middleware_encapsulation: ' . config('cms.route_middleware_encapsulation'));
        Route::group(['middleware' => explode(',', config('cms.route_middleware_encapsulation'))], function () {
            Route::group(['prefix' => 'language'], function () {
                Route::post('/switch', [LanguageController::class, 'update'])->name('post.language.switch');
            });
            if ( ! empty( config('cms.spatie_permission'))) {
                if( config('cms.route_debug')) error_log('CMS spatie_permission: ' . config('cms.spatie_permission'));
                Route::group(['middleware' => ['permission:' . strtolower(config('cms.spatie_permission'))]], function () {
                    include('cmsRoutes.php');
                });
            } else {
                include('cmsRoutes.php');
            }
        });
    } else {
        if( config('cms.route_debug')) error_log('CMS NO route_middleware_encapsulation');
        Route::group(['prefix' => 'language'], function () {
            Route::post('/switch', [LanguageController::class, 'update'])->name('post.language.switch');
        });
        if (!empty(config('cms.spatie_permission'))) {
            Route::group(['middleware' => ['permission:' . strtolower(config('cms.spatie_permission'))]], function () {
                include('cmsRoutes.php');
            });
        } else {
            include('cmsRoutes.php');
        }

    }
}
