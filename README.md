# CMS package 
A package to give laravel\blade i18n and wordpress like abilities which connects to the kcs-content manager backend, acting as a headless cms
  providing: remote or on page content editing, dynamic pages based on page templates, i18n and more
It works based on a custom blade tag that translates the parameters into content that will be saved locally as a resource for subsequent page calls
In essence it works the same way as the current laravel default translation system, just way more extended. 
  
This version also includes response-format to create a unified API response format including uses for async based operations

router file: web.php
```
Route::group(['middleware' => [ 'web', 'language' ]], function () {
  Route::post('/test/communication', [ContentController::class, 'testEndpoint'])->name('cms.endpoint.test')->middleware(['response-format:default']);
});
```

Blade example:
```
@section('title')
     @c(['mimetype' => 'text/plain', 'key' => 'title', 'page' => $page, 'editable'=> false])
@endsection
@c(['mimetype' => 'text/html', 'editable' => true, 'key' => 'hero', 'page' => $page, 'default' => '
        <main role="main" class="hero">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="hero-content">
                            <h1 class="__title">VendiFill Machines</h1>
                            <p class="__subtitle">The Best Vending Machines for Your Business</p>
                            <p> tralalalala</p>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 d-none d-sm-block">
                        <div class="hero-img">
                            <img class="lazy" src="/img/blank.gif" data-src="/img/img_4.png" alt="VendiFill Machines" width="719" height="741">
                        </div>
                    </div>
                </div>
            </div>
        </main>
    '])
```
# Requirements: 

laravel spatie mermission installed with an admin or developer role defined.


# Current known issues:

1. watch the single quote and double quote layout. the default content must contain double qoutes otherwise it breaks.
2. the content can currently not contain nested blade directives.
3. response-format is currently buildin, but idealy this must be a seperate package to avoid conflict


# todo: 

Rename response-format to make it compatible with extenral response-format package


# upcomming features:

A drag and drop UI interface to expand dynamic page and ad form creation capabilities.
( https://github.com/kristijanhusak/laravel-form-builder )



## Shout out:
https://spatie.be/open-source/packages
awesome work guys!
