<?php

namespace Khessels\Cms\Controllers;

use DOMDocument;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Khessels\Cms\Controllers\Controller as PackageController;

/* TODO-LIST
TODO: CHANGE PAGES CACHE to RESOURCE FILE
*/
class ContentController extends PackageController
{
    public $acceptedImageFileExtensions = null;

    public function __construct()
    {
        $this->acceptedImageFileExtensions = config('cms.accepted_image_extensions');
        // parent::__construct();
        // $this->middleware('role:admin|developer');
    }
    /*
public function tag_update_direct(Request $request, $app, $id)
    {
        $all = $request->all();
        // get element from resource file
        $cache = Cache::get('content');

        foreach ($cache as $item) {
            if ($item['id'] == $id) {
                // the tag['value'] can contain a null value indicating the tag['value'] has not been set.
                //   In that case use the tag['default'] value
                $strElement = is_null($all['value']) ? $item['default'] : $all['value'];
                $url = config('cms.domain') . '/api/tag/direct/' . $app . '/' . $id;

                $response = Http::withToken(config('cms.token'))->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'x-dev' => config('cms.dev'),
                    'x-app' => config('cms.app')
                ])->patch(
                    $url,
                    ['value' => $strElement]
                );
                $json = $response->json();
                if (! empty($json)) {
                    error_log(json_encode($json));
                }

                // reload resource files
                $this->collection_reload($request);
                return 'OK';
            }
        }
        return 'NOT FOUND';
    }

    */
    public function content_editor( Request $request, $id)
    {
        $files = Storage::disk("resources")->allFiles();
        $languages = [];
        foreach ( $files as $file) {
            $content = null;
            if( $file !== 'pages') {
                $languages[] = $file;
                $serialized = Storage::disk('resources')->get( $file);
                $lines = unserialize( $serialized);
                foreach( $lines as $line){
                    if( $line['id'] == $id){
                        $content = $line['value'];
                    }
                }
            }
        }
        return view('package-views::content-editor')
            ->with('content', $content)
            ->with( 'languages', $languages);
    }

    public function createImagesDirectory(Request $request)
    {
        $directory = $request->get('directory');
        if (! empty($directory)) {
            Storage::disk(config("cms.images_disks")[ 0])->makeDirectory($directory, 0777, true);
        }
        if ($request->wantsJson()) {
            // Handle JSON response
            return response()->json(['success' => true]);
        }
        return redirect('/cms#images_tab');
    }

    public function deleteImagesDirectory(Request $request)
    {
        $directory = $request->get('directory');
        if (! empty($directory)) {
            Storage::disk(config("cms.images_disks")[ 0])->deleteDirectory($directory);
        }
        if ($request->wantsJson()) {
            // Handle JSON response
            return response()->json(['success' => true]);
        }
        return redirect('/cms#images_tab');
    }
    public function setImagesDirectory(Request $request)
    {
        $directory = $request->get('directory');
        if (! empty($directory)) {
            Session::put('cms.images.directory', $directory);
        }
        if ($request->wantsJson()) {
            // Handle JSON response
            return response()->json(['success' => true]);
        }
        return redirect('/cms#images_tab');
    }
    public function getImagesDirectories(Request $request)
    {
        $directories = Storage::disk(config("cms.images_disks")[ 0])->allDirectories();
        if ($request->wantsJson()) {
            // Handle JSON response
            return response()->json(['directories' => $directories]);
        }
        return redirect('/cms#images_tab');
    }

    private function getResources($request, $directory)
    {
        $filters = $request->all();

        $url = Storage::disk(config("cms.images_disks")[ 0])->url("");
        $storageAllResources = Storage::disk(config("cms.images_disks")[ 0])
            ->allFiles($directory);

        // iterate the images and create a list of resources
        $resources = [];
        foreach ($storageAllResources as $resource) {
            $exploded = explode('/', $resource);
            $baseFilename = $exploded[ count($exploded) - 1];
            $explodedBaseFileName = explode('.', $baseFilename);
            if (in_array(strtolower($explodedBaseFileName[ count($explodedBaseFileName) - 1]), $this->acceptedImageFileExtensions)) {
                $obj = [];
                $obj[ 'url'] = $url .$resource;
                $obj[ 'base_url'] = $url;
                $obj[ 'filename'] = $baseFilename;
                $obj[ 'disk'] = config("cms.images_disks")[ 0];
                if (array_search($resource . '.json', $storageAllResources)) {
                    $obj[ 'data'] = json_decode(Storage::disk(config("cms.images_disks")[ 0])->get($resource . '.json'), true);
                }
                $resources[] = $obj;
            }
        }
        return $resources;
    }

    private function templateNames($request)
    {
        $templateNames = [];
        $directoryPath = resource_path('views') . '/templates';
        $files = File::allFiles($directoryPath);
        foreach ($files as $file) {
            $fileName = $file->getFilename();
            $templateNames[] = explode('.', substr($fileName, 0, strlen($directoryPath)))[ 0];
        }
        return $templateNames;
    }
    public function updateImageAttributes(Request $request)
    {
        $data       = $request->all();
        $directory  = $request->directory;
        $filename   = $data[ 'filename'];
        $language   = app()->getLocale();
        if (! empty($data[ 'language'])) {
            $language = $data[ 'language'];
        }
        $disk       = config("cms.images_disks")[ 0];
        $file       = Storage::disk($disk)->get($directory . '/' . $filename . '.json');

        $json = [];
        if (! empty($file)) {
            $json = json_decode($file, true);
        }
        if ($language === 'all') {
            foreach (config('cms.available_locales') as $lang) {
                if (! empty($data[ 'title'])) {
                    $json[ $lang][ 'title'] = $data[ 'title'];
                }
                if (! empty($data[ 'alt'])) {
                    $json[ $lang][ 'alt'] = $data[ 'alt'];
                }
            }
        } else {
            if (! empty($data[ 'title'])) {
                $json[ $language][ 'title'] = $data[ 'title'];
            }
            if (! empty($data['alt'])) {
                $json[ $language][ 'alt'] = $data[ 'alt'];
            }
        }
        if (! empty($data['tags'])) {
            $json[ 'tags'] = explode(',', $data[ 'tags']);
        }
        Storage::disk($disk)->put($directory . '/' .  $filename . '.json', json_encode($json));

        if ($request->wantsJson()) {
            // Handle JSON response
            return response()->json(['success' => true]);
        }
        return redirect('/cms#content_tab');
    }
    public function index(Request $request)
    {
        $templateNames = $this->templateNames($request);
        $directory = '';
        if ($request->has('directory')) {
            $directory = $request->get('directory');
        }
        $resources = $this->getResources($request, $directory);
        $directories = Storage::disk(config("cms.images_disks")[ 0])->allDirectories();
        return view('package-views::cms', ['directory' => $directory])
            ->with('template_pages', $templateNames)
            ->with('resourceList', $resources)
            ->with('page', 'image-management')
            ->with('accepted_files', $this->acceptedImageFileExtensions)
            ->with('directories', $directories)
            ->with('directory', $directory)
            ->with('resources', []);
    }
    public function store(Request $request)
    {
        $data = json_decode($request->data);

        // Process each uploaded image
        foreach ($request->file('files') as $image) {
            // Generate a unique name for the image
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

            // Move the image to the desired location
            Storage::disk(config("cms.images_disks")[ 0])->putFileAs($data->directory, $image, $imageName);
        }
        return response()->json(['success' => `[]]`]);
    }

    public function removeImages(Request $request)
    {
        $ids = explode(',', $request->query('ids'));
        $storageAllResources = Storage::disk(config("cms.images_disks")[ 0])
            ->allFiles();
        foreach ($storageAllResources as $resource) {
            $filename = explode('/', $resource);
            $filename = $filename[ count($filename) - 1];
            if (in_array(strtolower($filename), $ids)) {
                // remove the image
                Storage::disk(config("cms.images_disks")[ 0])->delete($resource);
                // remove the json file
                Storage::disk(config("cms.images_disks")[ 0])->delete($filename . '.json');
            }
        }

        if ($request->wantsJson()) {
            // Handle JSON response
            return response()->json(['success' => true]);
        }
        return redirect('/cms#images_tab');
    }
    public function moveImages(Request $request)
    {
        foreach ($request->ids as $image) {
            $moveto = $request->target_directory;

            Storage::disk(config("cms.images_disks")[ 0])->move($image, $moveto . '/' . $image);
            Storage::disk(config("cms.images_disks")[ 0])->move($image . ".json", $moveto . '/' . $image . '.json');
        }
        if ($request->wantsJson()) {
            // Handle JSON response
            return response()->json(['success' => true]);
        }
        return redirect('/cms#images_tab');
    }

    public function getPageFromCMS(Request $request, $page)
    {
        // $pages = Cache::get('pages', []);
        $pages = unserialize( Storage::disk('resources')->get( 'pages'));
        foreach ($pages as $oPage) {
            if (strtolower($page) === strtolower($oPage['page'])) {
                return view("templates." . $oPage['template'])->with('page', $page);
            }
            $properties = json_decode($oPage['properties'], true);
            foreach ($properties['urls'] as $url) {
                if (strtolower($page) === strtolower($url)) {
                    return view("templates." . $oPage['template'])->with('page', $page)->with('template', $oPage['template']);
                }
            }
        }
        if ($request->wantsJson()) {
            return response('Resource not found', 404);
        }
        abort(404);
    }

    public function testEndpoint(Request $request)
    {
        $response = Http::withToken(config('cms.token'))->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'x-dev' => config('cms.dev'),
            'x-app' => config('cms.app')
        ])->post(config('cms.domain') . '/api/helo');
        $responseData = $response->json();
        if ($response->successful()) {
            return response('Endpoint is reachable: ' . $response->body());
        } elseif ($response->clientError()) {
            return response('Client error: ' . $responseData['message'] ?? '', 300);
        } elseif ($response->serverError()) {
            return response('Client error: ' . $responseData['message'] ?? '', 400);
        }
        return response('Unknown Error', 500);
    }

    public function db_populate_from_resources(Request $request)
    {
        $all = $request->all();
        if ($request->has('app')) {
            $files = Storage::disk("resources")->allFiles();
            if ( empty( $files)) {
                $this->alertNotification('No resources found to populate database', 'warning');
                return redirect('/cms#content_tab');
            }
            foreach ($files as $file) {
                $serialized = Storage::disk('resources')->get($file);
                $r = Http::withToken(config('cms.token'))->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'x-dev' => config('cms.dev'),
                    'x-app' => config('cms.app')
                ])->post(config('cms.domain') . '/api/database/populate/from-resources', ['language' => $file, 'data' => $serialized]);
            }
            $this->alertNotification('Database has been repopulated', 'success');
        }
        return redirect('/cms#content_tab');
    }
    public function db_delete(Request $request)
    {
        if ($request->has('app')) {
            $r = Http::withToken(config('cms.token'))->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'x-dev' => config('cms.dev'),
                'x-app' => config('cms.app')
            ])->delete(config('cms.domain') . '/api/database');
            $this->alertNotification('Database has been deleted !!!', 'warning');
        }
        return redirect('/cms#content_tab');
    }
    public function setInnerHTML($element, $html)
    {
        @$fragment = $element->ownerDocument->createDocumentFragment();
        @$fragment->appendXML($html);
        @$clone = $element->cloneNode(); // Get element copy without children
        @$clone->appendChild($fragment);
        @$element->parentNode->replaceChild($clone, $element);
        return $element;
    }
    public function tag_update_direct(Request $request, $app, $id)
    {
        $all = $request->all();
        // get element from resource file
        $cache = Cache::get('content');

        foreach ($cache as $item) {
            if ($item['id'] == $id) {
                // the tag['value'] can contain a null value indicating the tag['value'] has not been set.
                //   In that case use the tag['default'] value
                $strElement = is_null($all['value']) ? $item['default'] : $all['value'];
                $url = config('cms.domain') . '/api/tag/direct/' . $app . '/' . $id;

                $response = Http::withToken(config('cms.token'))->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'x-dev' => config('cms.dev'),
                    'x-app' => config('cms.app')
                ])->patch(
                    $url,
                    ['value' => $strElement]
                );
                $json = $response->json();
                if (! empty($json)) {
                    error_log(json_encode($json));
                }

                // reload resource files
                $this->collection_reload($request);
                return 'OK';
            }
        }
        return 'NOT FOUND';
    }

    public function cms_enable(Request $request)
    {
        // disable content tag collection
        Session::put('cms.enable', true);
        $this->alertNotification('CMS Enabled.');
        return redirect('/cms#content_tab');
    }

    public function cms_disable(Request $request)
    {
        // disable content tag collection
        Session::put('cms.enable', false);
        $this->alertNotification('CMS Disabled.');
        return redirect('/cms#content_tab');
    }

    public function artisan_optimize(Request $request)
    {
        // run artisan optimize so that tag changes will be propagated
        Artisan::call('optimize', ['--quiet' => true]);
        $this->alertNotification('Artisan optimized.');
        return redirect('/cms#content_tab');
    }

    public function collection_enable(Request $request, $language = '*')
    {
        // enable content tag collection
        Session::put('cms.collection.enabled', true);
        $this->alertNotification('CMS Tag collection started');
        return redirect('/cms#content_tab');
    }
    public function collection_disable(Request $request)
    {
        // disable content tag collection
        Session::put('cms.collection.enabled', false);
        $this->alertNotification('CMS Tag collection stopped', 'warning');
        return redirect('/cms#content_tab');
    }
    public function collection_delete(Request $request, $language = '*')
    {
        // delete tag collection
        Cache::delete('cms.collection');
        $this->alertNotification('CMS Tag collection deleted', 'warning');
        return redirect('/cms#content_tab');
    }
    public function collection_upload(Request $request, $language = '*')
    {
        $lang = Lang::locale();
        if (! empty($language)) {
            $lang = $language;
        }
        $data = Cache::get('cms.collection');
        if (! empty($data)) {
            $r = Http::withToken(config('cms.token'))->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'x-dev' => config('cms.dev'),
                'x-app' => config('cms.app')
            ])
                ->post(config('cms.domain') . '/api/expressions', ['expressions' => $data]);
            Cache::delete('cms.collection');
            // disable content tag collection
            Session::put('cms.collection.enabled', false);
        }
        $this->alertNotification('CMS Tag collection uploaded !! Collection stopped', 'error');
        return redirect('/cms#content_tab');
    }
    public function collection_reset(Request $request, $language = '*')
    {
        $lang = Lang::locale();
        if (! empty($language)) {
            $lang = $language;
        }
        Session::put('cms.collection.enabled', false);
        // delete language files
        if ($lang !== '*') {
            Storage::disk('resources')->delete( $lang);
        } else {
            $files = Storage::disk("resources")->allFiles();
            foreach ($files as $file) {
                if( $file !== 'pages') {
                    Storage::disk('resources')->delete($file);
                }
            }
        }
        $this->alertNotification('CMS Tag collection reset');
        return redirect('/cms#content_tab');
    }
    public function collection_reload(Request $request, $language = '*')
    {
        $lang = Lang::locale();
        if (! empty($language)) {
            $lang = $language;
        }

        // delete language files
        if ( $lang !== '*') {
            Storage::disk('resources')->delete( $lang);
        } else {
            $files = Storage::disk("resources")->allFiles();
            foreach ($files as $file) {
                if( $file !== 'pages') {
                    Storage::disk('resources')->delete( $file);
                }
            }
        }
        // retrieve new content
        $o = new ContentController();
        $o->retrieveContent($lang);
        $this->alertNotification('CMS Tag collection reloaded');

        return redirect('/cms#content_tab');
    }

    public function retrieveContent($language)
    {
        $parm = '';
        if (! empty($language)) {
            if ($language !== '*') {
                $parm = '/' . $language;
            }
        }

        $response = Http::withToken(config('cms.token'))->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'x-app' => config('cms.app'),
            'x-dev' => config('cms.dev'),
        ])->get(config('cms.domain') . '/api/management/content' . $parm);
        $tags = $response->json();
        $items = [];
        foreach ($tags as $tag) {
            if ($tag['language'] === 'es' && $tag['key'] === 'banner') {
                $s = '';
            }
            if ($tag['language'] === null) {
                $items[ 'language-generic'][] = $tag;
            } else {
                $items[ $tag[ 'language']][] = $tag;
            }
        }
        foreach ($items as $page => $item) {
            Storage::disk('resources')->put($page, serialize($item));
        }
        return 'OK';
    }
    public static function _getTagAndValue($expression, $content)
    {
        // filter content array on tag key (like 'title')
        $tags = array_filter($content, function ($obj) use ($expression) {
            return $obj[ 'key'] === $expression[ 'key'];
        });
        // using previously filtered content array filter out tags that don't have the required language
        //    (if you are looking for an english tag, you don't want to include dutch tags)
        $tags = array_filter($tags, function ($obj) use ($expression) {
            $language = Lang::locale();
            if (! empty($expression[ 'language'])) {
                $language = $expression[ 'language'];
            }
            if (empty($obj[ 'language'])) {
                return true;
            }
            return ($obj[ 'language'] === $language || $obj[ 'language'] === null);
        });
        // using previously filtered content array filter out tags that don't have the required page
        //    (if you are looking for an index page, you don't want to include contact page tags)
        $tags = array_filter($tags, function ($obj) use ($expression) {
            $page = null;
            if (! empty($expression[ 'page'])) {
                $page = $expression[ 'page'];
            }
            if (empty($obj[ 'page'])) {
                return true;
            }
            return ($obj[ 'page'] === $page);
        });
        // sort array on language and page (so 2 times) so that the null values are at the bottom
        usort($tags, function ($a, $b) {
            return $a[ 'language'] === $b[ 'language'];
        });
        usort($tags, function ($a, $b) {
            return $a[ 'page'] === $b[ 'page'];
        });
        // now we can select the first found tag (or not of course)
        $foundTag = sizeof($tags) > 0 ? $tags[ 0] : null;
        if ($foundTag !== null) {
            $val = ($foundTag[ 'value'] !== null) ? $foundTag[ 'value'] :
                (! empty($expression[ 'default']) ? $expression[ 'default'] : $expression[ 'key']);
        } else {
            $val = ! empty($expression[ 'default']) ? $expression[ 'default'] : $expression[ 'key'];
        }
        return [ 'tag' => $foundTag, 'value' => $val];
    }
    private static function addToCMSCollectionCache($expression): void
    {
        // save tag to expressions cache
        if ($expression['key'] !== '404') {
            $serialized = Cache::get('cms.collection');
            $lines = [];
            if (!empty($serialized)) {
                $lines = unserialize($serialized);
            }
            if (empty($expression['language'])) {
                $expression['language'] = Lang::locale();
            }
            $lines[] = json_encode($expression);
            $collect = true;
            if (isset($expression['collection'])) {
                if ($expression['collection'] == false || $expression['collection'] === 'disabled') {
                    $collect = false;
                }
            }
            if ($collect) {
                Cache::put('cms.collection', serialize($lines));
            }
        }
    }
    public static function updateImageElement($content)
    {
        $dom = new DomDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $content['value'], LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOERROR);

        // add cms attributes to parent element
        $node = $dom->documentElement;

        // iterate images, extract source, lookup image data for title and alt attributes
        $imgs = $node->getElementsByTagName("img");
        foreach ($imgs as $index => $img) {
            $src        = $img->getAttribute('src');
            $language   = app()->getLocale();
            $exploded   = explode('/', $src);
            $filename   = $exploded[ count($exploded) - 1] . '.json';
            $data       = json_decode(Storage::disk(config("cms.images_disks")[ 0])->get($filename), true);

            if (! empty($data)) {
                if (! empty($data[ $language])) {
                    if (! empty($data[ $language]['title'])) {
                        $imgs[ $index]->setAttribute('title', $data[ $language]['title']);
                    }
                    if (! empty($data[ $language]['alt'])) {
                        $imgs[ $index]->setAttribute('alt', $data[ $language]['alt']);
                    }
                }
                if (! empty($data['tags'])) {
                    $imgs[ $index]->setAttribute('data-tags', implode(',', $data['tags']));
                }
            }
        }
        $fragment = $dom->saveHTML($node);
        return $fragment;
    }

    public static function addCMSAttributesToContent($content): false|string
    {
        try {
            $dom = new DomDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML('<?xml encoding="utf-8" ?>' . $content['value'], LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOERROR);

            // add cms attributes to parent element
            $node = $dom->documentElement;
            $class = $node->getAttribute('class');

            $node->setAttribute('class', 'cms ' . $class);
            $node->setAttribute('data-cms-id', $content['tag']['id']);
            $node->setAttribute('data-cms-mimetype', $content['tag']['mimetype']);

            $fragment = $dom->saveHTML($node);
            return $fragment;
        } catch (Exception $e) {
            $s = $e->getMessage();
        }
        return false;
    }
    public static function _translate($expression, $content, $wrapped = false)
    {
        try {
            $foundContent = self::_getTagAndValue($expression, $content);
            $mimetype = ! empty($expression[ 'mimetype' ]) ? $expression[ 'mimetype' ] : 'text/html';

            if (Session::get('cms.collection.enabled')) {
                self::addToCMSCollectionCache($expression);
            }
            if (! $wrapped && $mimetype === 'text/html' &&  config('cms.image_management')) {
                return self::updateImageElement($foundContent);
            }
            return  $foundContent;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return [];
        }
    }
    public static function translate($expression)
    {
        try {
            $resources = Cache::get('content') ;
            $resources = empty($resources) ? [] : $resources;
            $content = self::_translate($expression, $resources, Session::get('cms.enable'));
            if (! is_array($content)) {
                return  $content;
            }
            $editable = ! isset($expression[ 'editable']) ? true : $expression[ 'editable'];
            if ($content[ 'tag'] === null || $editable === false) {
                return  $content['value'];
            }
            if (empty($expression['editable'])) {
                return  $content['value'];
            }
            if (! empty($content['tag'])) {
                switch ($content['tag']['mimetype']) {
                    case 'text/html':
                        return self::addCMSAttributesToContent($content);
                    case 'text/plain':
                        return "<span class='cms' data-cms-id='" . $content['tag']['id'] . "' data-cms-mimetype='" . $content['tag']['mimetype'] . "' >" . $content['value'] . "</span>";
                    default:
                        return $content['value'];
                }
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
        return '';
    }
    public static function _retrievePages()
    {
        $response = Http::withToken(config('cms.token'))->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'x-app' => config('cms.app')
        ])->get(config('cms.domain') . '/api/page/list/ACTIVE');
        $pages = $response->json();
        Storage::disk('resources')->put( 'pages', serialize( $pages));
        //Cache::set('pages', $pages);
        return redirect('/cms#pages_tab');
    }

    public function deletePage(Request $request, $page = null)
    {
        if (empty($page)) {
            $page = $request->get('page');
        }
        $response = Http::withToken(config('cms.token'))->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'x-dev' => config('cms.dev'),
            'x-app' => config('cms.app')
        ])->delete(config('cms.domain') . '/api/page/' . $page);
        return redirect('/cms#pages_tab');
    }
    public function addPage(Request $request)
    {
        $all = $request->all();
        $all[ 'page'] = STR::slug($all[ 'page']);
        if (! $request->has('properties')) {
            $all[ 'properties'] = json_encode(['urls' => [ $all[ 'page']]]);
        } else {
            if (empty($all[ 'properties' ][ 'urls'])) {
                $all[ 'properties'][ 'urls'] = [ $all[ 'page']];
            }
        }
        $response = Http::withToken(config('cms.token'))->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'x-dev' => config('cms.dev'),
            'x-app' => config('cms.app')
        ])->post(config('cms.domain') . '/api/page', $all);
        return redirect('/cms#pages_tab');
    }
    public function getPage(Request $request, $page)
    {
        $response = Http::withToken(config('cms.token'))->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'x-app' => config('cms.app')
        ])->post(config('cms.domain') . '/api/page/' . $page);
        return 'OK';
    }
    public function setPageActiveState(Request $request, $page, $status = 'ACTIVE')
    {
        $response = Http::withToken(config('cms.token'))->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'x-dev' => config('cms.dev'),
            'x-app' => config('cms.app')
        ])->patch(config('cms.domain') . '/api/page/' . $page . '/active/' . $status);
        return 'OK';
    }

    public function clearPageCache(Request $request)
    {
        // Cache::clear('pages');
        Storage::disk( 'resources')->delete( 'pages');
        return redirect('/cms#pages_tab');
    }
}
