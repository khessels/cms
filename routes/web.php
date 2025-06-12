<?php

use Illuminate\Support\Facades\Route;
use khessels\cms\Controllers\ContentController;
use khessels\cms\Controllers\LanguagesController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

//Route::group(['middleware' => [ 'language']], function () {

//Route::any('/{page}', 'ContentController@getPageFromCMS')->where('any', '.*');

Route::group(['prefix' => 'language'], function () {
    Route::post('/switch', [LanguagesController::class, 'languageSwitch'])->name('post.language.switch');
});
Route::group(['middleware' => [ 'web']], function () {
    Route::group(['middleware' => [ 'role:admin']], function () {
        // Route::get('/test-2', function () {
        //     //$roles = Auth::user()->getRoleNames();
        //     $roles = Auth::user()->getRoleNames();
        //     error_log( print_r( $roles,true));

        //     //Log::info( print_r( $user, true));
        //     return view('package-views::test-2')->with('roles', $roles);
        //     //return view('views::test-2');
        // })->name('test-2');

        Route::get('cms', [ContentController::class, 'index'])->name('cms');
        Route::group(['prefix' => 'cms'], function () {
            Route::post('/images/action', [ContentController::class, 'imagesAction'])->name('cms.images.action');
            Route::post('/images/directory', [ContentController::class, 'createImagesDirectory'])->name('cms.images.directory.create');
            Route::delete('/images/directory', [ContentController::class, 'deleteImagesDirectory'])->name('cms.images.directory.delete');

            Route::get('/', [ContentController::class, 'index'])->name('cms.index');
            Route::get('/enable', [ContentController::class, 'cms_enable'])->name('cms.enable');
            Route::get('/disable', [ContentController::class, 'cms_disable'])->name('cms.disable');

            Route::get('/artisan/optimize', [ContentController::class, 'artisan_optimize'])->name('cms.artisan.optimize');

            Route::get('/collection/enable', [ContentController::class, 'collection_enable'])->name('cms.collection.enable');
            Route::get('/collection/disable', [ContentController::class, 'collection_disable'])->name('cms.collection.disable');
            Route::get('/collection/delete', [ContentController::class, 'collection_delete'])->name('cms.collection.delete');
            Route::get('/collection/upload', [ContentController::class, 'collection_upload'])->name('cms.collection.upload');
            Route::get('/collection/reset', [ContentController::class, 'collection_reset'])->name('cms.collection.reset');
            Route::get('/collection/reload', [ContentController::class, 'collection_reload'])->name('cms.collection.reload');

            Route::patch('/tag/direct/{app}/{id}', [ContentController::class, 'tag_update_direct'])->name('cms.tag.update.direct');

            Route::delete('/database', [ContentController::class, 'db_delete'])->name('cms.database.delete');
            Route::post('/page/add', [ContentController::class, 'addPage'])->name('cms.page.add');
            Route::delete('/page/delete/{page}', [ContentController::class, 'deletePage'])->name('cms.page.delete');
            Route::delete('/page/cache', [ContentController::class, 'clearPageCache'])->name('cms.page.cache.clear');
            Route::get('/image/management', [ContentController::class, 'imageManagement'])->name('cms.image.management.get');
            Route::post('/image/management', [ContentController::class, 'imageManagement'])->name('cms.image.management.post');
            Route::post('/dropzone/store', [ContentController::class, 'store'])->name('cms.dropzone.store');
            Route::post('/image/data', [ContentController::class, 'setImageData'])->name('cms.image.data.post');
        });
    });
});
Route::get('/cms/{page}', [ContentController::class, 'getPageFromCMS'])->name('cms.page');
Route::get('/cms/image/data', [ContentController::class, 'getImageData'])->name('cms.image.data.get');
    //Route::get('/{page}', [ContentController::class, 'getPageFromCMS'])->name('page');
//});

 Route::fallback( [ContentController::class, 'getPageFromCMS'] );
