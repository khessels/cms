@extends('package-views::layouts.cms')

@section('main')
    <container class="container-fluid">
        <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                    <a href="/cms/collection/reset">Collection Reset</a><br>
                    <a href="/cms/collection/reload">Collection Reload</a><br>
                    <a href="/cms/collection/upload">Collection upload</a><br>
                    <a href="/cms/collection/delete">Collection delete</a><br>
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                    <a href="/cms/collection/enable">Collection enable</a><br>
                    <a href="/cms/collection/disable">Collection disable</a><br>
                    <br>
                    <a href="/cms/enable">CMS Enable</a><br>
                    <a href="/cms/disable">CMS Disable</a><br>
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                    <h2 style="color: darkred">*** BE CAREFULL ***</h2>
                    <a href="/cms/artisan/optimize">Artisan Optimize</a><br>
                    <form action="/cms/database" method="post">
                        @csrf
                        <input type="hidden" name="_method" value="delete">
                        <input type="hidden" name="app" value="{{ config('cms.app')}}">
                        <input style="width:200px; color: darkred"  type="submit" value="Delete Database">
                    </form>
                </div>
            </div>


            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                    <a href="/cms/image/management" target="_blank">Images management</a>
                </div>
    {{--            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">--}}
    {{--                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />--}}
    {{--            </div>--}}
    {{--            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">--}}
    {{--                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />--}}
    {{--            </div>--}}
            </div>
            <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <div>
                    <button class="btn btn-primary add-page">Add new page</button>&nbsp;
                    <form method="POST" action="/cms/page/cache">
                        @csrf
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                        <button type="submit" class="btn btn-primary">Reload page cache</button>
                    </form>
                </div>

                <table id="tbl_pages">
                    <thead>
                        <tr>
                            <td>Id</td>
                            <td>P.Id</td>
                            <td>Page</td>
                            <td>Template</td>
                            <td>Properties</td>
                            <td>Roles</td>
                            <td>Publish@</td>
                            <td>Expire@</td>
                            <td>Last used</td>
                            <td>Active</td>

                        </tr>
                    </thead>
                    <tbody>
                        @if( ! empty( Cache::get('pages')))
                            @foreach( Cache::get('pages') as $page)
                                <tr>
                                    <td>{{ $page['id'] }}</td>
                                    <td>{{ $page['parent_id'] }}</td>
                                    <td>{{ $page['page'] }}</td>
                                    <td>{{ $page['template'] }}</td>
                                    <td>{{ $page['properties'] ?? '' }}</td>
                                    <td>{{ $page['roles'] }}</td>
                                    <td>{{ $page['publish_at'] }}</td>
                                    <td>{{ $page['expire_at'] }}</td>
                                    <td>{{ $page['last_seen_at'] }}</td>
                                    <td>{{ $page['status'] }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div id="mdl_add_page" class="modal" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST" action="/cms/page/add">
                    <input type="hidden" name="_method" value="POST">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add page</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @csrf
                            <table>
                                <tr>
                                    <td>
                                        name
                                    </td>
                                    <td>
                                    </td>
                                    <td>
                                        <input class="form-control" type="text" name="page">
                                    </td>
                                    <td>
                                        (will be slugified)

                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Parent ID
                                    </td>
                                    <td>
                                    </td>
                                    <td>
                                        <input class="form-control" type="text" name="parent_id">
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        User ID
                                    </td>
                                    <td>
                                    </td>
                                    <td>
                                        <input class="form-control" type="text" name="user_id">
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Template
                                    </td>
                                    <td>
                                    </td>
                                    <td>
                                        <select name="template" class="form-select">
                                            @foreach( $template_pages as $template_page)
                                                <option value="{{ $template_page }}">{{ $template_page }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Status
                                    </td>
                                    <td>
                                    </td>
                                    <td>
                                        <select name="status" class="form-select">
                                            <option selected value="active">ACTIVE</option>
                                            <option value="inactive">INACTIVE</option>
                                        </select>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Roles
                                    </td>
                                    <td>
                                    </td>
                                    <td>
                                        <select name="roles" class="form-select">
                                            <option selected value="">ALL</option>
                                            <option value="authenticated">Authenticated</option>
                                            <option value="admin">Admin</option>
                                            <option value="client">Client</option>
                                        </select>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Publish @
                                    </td>
                                    <td>
                                    </td>
                                    <td>
                                        <input class="form-control" type="datetime-local" name="publish_at">
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Expire @
                                    </td>
                                    <td>
                                    </td>
                                    <td>
                                        <input class="form-control" type="datetime-local" name="expire_at">
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                            </table>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </container>
@endsection

@section('footer-js')
    <script>
        let app = '{{ config('cms.app')}}'
        let body = $('body');

        let mdlAddPage = new bootstrap.Modal(document.getElementById("mdl_add_page"), {});
        let table = new DataTable('#tbl_pages');

        body.on('click', '.add-page', function( e){
            mdlAddPage.show();
        })
    </script>
@endsection
