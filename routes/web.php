<?php

use Illuminate\Support\Facades\Route;
use khessels\cms\Controllers\ContentController;
use khessels\cms\Controllers\LanguagesController;

Route::group(['middleware' => [ 'web', 'language' ]], function () {
    Route::group(['prefix' => 'language'], function () {
        Route::post('/switch', [LanguagesController::class, 'languageSwitch'])->name('post.language.switch');
    });
    Route::group(['middleware' => [ 'role:admin']], function () {
        Route::get('cms', [ContentController::class, 'index'])->name('cms');
        Route::group(['prefix' => 'cms'], function () {
            Route::group(['prefix' => 'images'], function () {
                Route::post('/action', [ContentController::class, 'imagesAction'])->name('cms.images.action');
                Route::post('/directory', [ContentController::class, 'createImagesDirectory'])->name('cms.images.directory.create');
                Route::delete('/directory', [ContentController::class, 'deleteImagesDirectory'])->name('cms.images.directory.delete');
            });

            Route::group(['prefix' => 'image'], function () {
                Route::get('/management', [ContentController::class, 'imageManagement'])->name('cms.image.management.get');
                Route::post('/management', [ContentController::class, 'imageManagement'])->name('cms.image.management.post');
                Route::post('/dropzone/store', [ContentController::class, 'store'])->name('cms.dropzone.store');
                Route::post('/data', [ContentController::class, 'setImageData'])->name('cms.image.data.post');
            });

            Route::group(['prefix' => 'collection'], function () {
                Route::get('/enable', [ContentController::class, 'collection_enable'])->name('cms.collection.enable');
                Route::get('/disable', [ContentController::class, 'collection_disable'])->name('cms.collection.disable');
                Route::get('/delete', [ContentController::class, 'collection_delete'])->name('cms.collection.delete');
                Route::get('/upload', [ContentController::class, 'collection_upload'])->name('cms.collection.upload');
                Route::get('/reset', [ContentController::class, 'collection_reset'])->name('cms.collection.reset');
                Route::get('/reload', [ContentController::class, 'collection_reload'])->name('cms.collection.reload');
            });

            Route::group(['prefix' => 'page'], function () {
                Route::post('/add', [ContentController::class, 'addPage'])->name('cms.page.add');
                Route::delete('/remove/{page?}', [ContentController::class, 'deletePage'])->name('cms.page.delete');
                Route::delete('/cache', [ContentController::class, 'clearPageCache'])->name('cms.page.cache.clear');
            });

            Route::patch('/tag/direct/{app}/{id}', [ContentController::class, 'tag_update_direct'])->name('cms.tag.update.direct');
            Route::delete('/database', [ContentController::class, 'db_delete'])->name('cms.database.delete');
            Route::get('/', [ContentController::class, 'index'])->name('cms.index');
            Route::get('/enable', [ContentController::class, 'cms_enable'])->name('cms.enable');
            Route::get('/disable', [ContentController::class, 'cms_disable'])->name('cms.disable');

        });
    });
    Route::get('/cms/{page}', [ContentController::class, 'getPageFromCMS'])->name('cms.page');
    Route::get('/cms/image/data', [ContentController::class, 'getImageData'])->name('cms.image.data.get');

    Route::fallback( [ContentController::class, 'getPageFromCMS'] );
});
