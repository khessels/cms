@extends('package-views::layouts.cms')

@section('main')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>CMS</h1>
                <p>Welcome to the CMS. Here you can manage your pages, collections, and other CMS related tasks.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="card" >
                    <div class="card-body">
                        <h5 class="card-title">Collection operations</h5>
                        <a href="/cms/collection/reset">Collection Reset</a><br>
                        <a href="/cms/collection/reload">Collection Reload</a><br>
                        <a href="/cms/collection/upload">Collection upload</a><br>
                        <a href="/cms/collection/delete">Collection delete</a><br>
                        <hr>
                        <a href="/cms/collection/enable">Collection enable</a><br>
                        <a href="/cms/collection/disable">Collection disable</a><br>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card" >
                    <div class="card-body">
                        <h5 class="card-title">In-Page content editor</h5>
                        <a href="/cms/enable">CMS Enable</a><br>
                        <a href="/cms/disable">CMS Disable</a><br>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card" >
                    <div class="card-body">
                        <h5 class="card-title" style="color: darkred">*** BE CAREFULL ***</h5>
                        <form action="/cms/database" method="post">
                            @csrf
                            <input type="hidden" name="_method" value="delete">
                            <input type="hidden" name="app" value="{{ config('cms.app')}}">
                            <input class="btn btn-warning" type="submit" value="Delete Database">
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card" >
                    <div class="card-body">
                        <h5 class="card-title">Image management</h5>
                        <a href="/cms/image/management" target="_blank">Images management</a>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-3">
                <h5 class="card-title">Page operations</h5>
                <button class="btn btn-primary add-page">Add new page</button>&nbsp;
                <form method="POST" action="/cms/page/cache">
                    @csrf
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <button type="submit" class="btn btn-primary">Reload page cache</button>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
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
                            <td>Action</td>
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
                                    <td><a class="page remove" href="javascript:void( 0)" data-id="{{ $page['id'] }}">Remove</a></td>
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
    </div>
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

        body.on( 'click', '.page.remove', function( e){
            //let id = $(this).data('id');
            let page = $(this).parents('tr').find('td:nth-child(3)').text();
            if( confirm('Are you sure you want to remove this page: ' + page + '?')){
                $.ajax({
                    url: '/cms/page/remove',
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}',
                        page: page
                    },
                    success: function( response){
                        toastr.warning('Remote Page removed, reload page cache to see changes.');
                        table.row($(this).parents('tr')).remove().draw();
                    },
                    error: function( response){
                        toastr.error('Error removing page');
                    }
                });
            }
        });
    </script>
@endsection
