@extends('package-views::layouts.cms')

@section('main')
    <div class="container">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="content-tab" data-bs-toggle="tab" data-bs-target="#content" type="button" role="tab" aria-controls="content" aria-selected="true">Content</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pages-tab" data-bs-toggle="tab" data-bs-target="#pages" type="button" role="tab" aria-controls="pages" aria-selected="false">Pages</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="images-tab" data-bs-toggle="tab" data-bs-target="#images" type="button" role="tab" aria-controls="images" aria-selected="false">Images</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="api-tab" data-bs-toggle="tab" data-bs-target="#api" type="button" role="tab" aria-controls="api" aria-selected="false">API</button>
            </li>
        </ul>
        <div class="tab-content mt-3" id="myTabContent">
            <div class="tab-pane fade show active" id="content" role="tabpanel" aria-labelledby="content-tab">
                <div class="row">
                    <div class="col-md-12">
                        <h1>CMS</h1>
                        Welcome to the CMS. Here you can manage your content, pages & images.
                        Use: We collect the available tags by enabling collection and open the pages (we open the page in each language).
                        The collected tags are then synced with the remote server by uploading them. Once synced we reload them again to create local resource files used by the pages.
                        Only then we will be able to use the in-page cms editor to edit each tag on the page.
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card" >
                            <div class="card-body">
                                <h5 class="card-title">Collection operations</h5>
                                <a href="/cms/collection/reset">Reset</a><br>
                                <a href="/cms/collection/reload">Reload</a><br>
                                <a href="/cms/collection/upload">Upload</a><br>
                                <a href="/cms/collection/delete">Delete</a><br>
                                <hr>
                                <a href="/cms/collection/enable">Start collecting</a><br>
                                <a href="/cms/collection/disable">Stop collecting</a><br>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card" >
                            <div class="card-body">
                                <h5 class="card-title">In-Page content editor</h5>
                                <a href="/cms/enable">Enable</a><br>
                                <a href="/cms/disable">Disable</a><br>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
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
                </div>
            </div>
            <div class="tab-pane fade" id="pages" role="tabpanel" aria-labelledby="pages-tab">
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
                                            <td>{{ $page['id'] ?? ''}}</td>
                                            <td>{{ $page['parent_id'] ?? ''}}</td>
                                            <td>{{ $page['page'] ?? ''}}</td>
                                            <td>{{ $page['template'] ?? ''}}</td>
                                            <td>--</td>
                                            <td>{{ $page['roles'] ?? ''}}</td>
                                            <td>{{ $page['publish_at'] ?? ''}}</td>
                                            <td>{{ $page['expire_at'] ?? ''}}</td>
                                            <td>{{ $page['last_seen_at'] ?? ''}}</td>
                                            <td>{{ $page['status'] ?? ''}}</td>
                                            <td><a class="page remove" href="javascript:void( 0)" data-id="{{ $page['id'] }}">Remove</a></td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="images" role="tabpanel" aria-labelledby="images-tab">
                <div class="section" style="margin: 20px;">
                    <div>

                        <div class="row">
                            <div class="col-12">
                                <form action="{{ route('cms.dropzone.store') }}" method="post" enctype="multipart/form-data" id="image-upload" class="dropzone">
                                    <input type="hidden" name="directory" value="{{ $directory }}">
                                    @csrf
                                </form>
                                <button id="uploadFile" class="btn btn-primary mt-1">Upload Images</button>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-4">
                                        <select id="sel_actions">
                                            <option value="" selected>{{__('Select')}}</option>
                                            <option value="delete" >{{__('Delete')}}</option>
                                            <option value="archive" >{{__('Archive')}}</option>
                                        </select>
                                        <button id="btn_action" type="button" class="btn btn-primary disabled">{{__('Apply')}}</button>
                                    </div>
                                    <div class="col-4">
                                        <p><strong>Click on the image preview to copy the link</strong></p>
                                    </div>
                                </div>
                                <table id="tbl_resources" class="table table-striped" style="width:100%; font-size:80%">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="cb_select_all"> {{__('Select')}}</th>
                                            <th>{{__('Preview')}}</th>
                                            <th>{{__('Lang.')}}</th>
                                            <th>{{__('Alt')}}</th>
                                            <th>{{__('Title')}}</th>
                                            <th>{{__('Dimensions')}}</th>
                                            <th>{{__('Tags')}}</th>
                                            <th>{{__('Actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach( $resourceList as $resource)
                                            @php
                                                if( ! isset( $resource[ 'filename']) || ! isset( $resource[ 'url'])){
                                                    continue;
                                                }
                                                $lang = app()->getLocale();
                                                foreach( config('cms.available_locales') as $locale){
                                                    if( ! isset( $resource[ 'data'][ $locale])){
                                                        $resource[ 'data'][ $locale] = [];
                                                    }
                                                    if( ! isset( $resource[ 'data'][ $locale][ 'alt'])){
                                                        $resource[ 'data'][ $locale][ 'alt'] = '';
                                                    }
                                                    if( ! isset( $resource[ 'data'][ $locale][ 'title'])){
                                                        $resource[ 'data'][ $locale][ 'title'] = '';
                                                    }
                                                    if( ! isset( $resource[ 'data'][ 'tags'])){
                                                        $resource[ 'data'][ 'tags'] = [];
                                                    }
                                                    if( ! isset( $resource[ 'data'][ 'width']) ){
                                                        $resource[ 'data'][ 'width'] = 0;
                                                    }
                                                    if( ! isset( $resource[ 'data'][ 'height']) ){
                                                        $resource[ 'data'][ 'height'] = 0;
                                                    }
                                                }
                                            @endphp
                                            <tr>
                                                <td><input type="checkbox" value="{{ $resource[ 'filename']}}" data-id="{{ $resource[ 'filename']}}"></td>
                                                <td><img data-id="{{ $resource[ 'filename']}}" data-src="{{ $resource[ 'url'] }}" class="resource img" style="height:40px" height="40px" src="{{ $resource[ 'url'] }}" alt="{{ $resource[ 'data'][ $lang][ 'alt'] ?? ''}}" title="{{ $resource[ 'data'][ $lang][ 'title'] ?? ''}}"></td>
                                                <td>
                                                    @foreach( config('cms.available_locales') as $locale)
                                                        {{ $locale}} <br>
                                                    @endforeach
                                                </td>
                                                <td>
                                                    @foreach( config('cms.available_locales') as $locale)
                                                        {{ $resource[ 'data'][ $locale][ 'alt']}} <br>
                                                    @endforeach
                                                </td>
                                                <td>
                                                    @foreach( config('cms.available_locales') as $locale)
                                                        {{ $resource[ 'data'][ $locale][ 'title']}} <br>
                                                    @endforeach
                                                </td>
                                                <td>
                                                    {{ $resource[ 'data'][ 'height']}}, {{ $resource[ 'data'][ 'width']}}<br>
                                                </td>
                                                <td>{{ implode(',', $resource[ 'data'][ 'tags'])}}</td>
                                                <td>
                                                    <a href="javascript:void(0)" class="image attributes update" data-id="{{ $resource[ 'filename']}}">Change</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <form action="/cms/image/management" method="POST">
                                    @csrf
                                    <h4>Image Directory</h4>
                                    <select class="form-select" name="directory" id="selDirectory" onchange="this.form.submit()">
                                        <option value="" selected>Root</option>
                                        @foreach( $directories as $_directory)
                                            @php
                                                $selected = '';
                                                if( $directory === $_directory){
                                                    $selected = 'selected';
                                                }
                                            @endphp
                                            <option value="{{ $_directory }}" {{$selected}}>{{ $_directory }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <form action="/cms/images/directory" method="POST">
                                    @csrf
                                    <input type="hidden" name="directory" value="{{ $directory }}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <h4>Remove Directory</h4>
                                    <select class="form-select" name="directory">
                                        @foreach( $directories as $_directory)
                                            @php
                                                $selected = '';
                                                if( $directory === $_directory){
                                                    $selected = 'selected';
                                                }
                                            @endphp
                                            <option value="{{ $_directory }}" {{$selected}}>{{ $_directory }}</option>
                                        @endforeach
                                    </select>
                                    <input  type="submit" class="btn btn-primary" value="Remove directory">
                                </form>
                                <form action="/cms/images/directory" method="POST">
                                    @csrf
                                    <input type="hidden" name="directory" value="{{ $directory }}">
                                    <h4>Create directory</h4>
                                    <select class="form-select" name="parent">
                                        <option value="" selected>Root</option>
                                        @foreach( $directories as $_directory)
                                            <option value="{{ $_directory }}">{{ $_directory }}</option>
                                        @endforeach
                                    </select>
                                    <input class="form-control" type="text" name="directory">
                                    <input type="submit" class="btn btn-primary" value="Create directory">
                                </form>

                            </div>
                            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                                <div>

                                    <table id="image-properties">
                                        <tr>
                                            <td>
                                                Language
                                            </td>
                                            <td>
                                                <form action="{{ route('post.language.switch') }}" method="POST">
                                                    <input type="hidden" name="directory" value="{{ $directory }}">
                                                    @csrf
                                                    <select name="language" id="language" class="form-select" onchange="this.form.submit()">
                                                        @foreach( config('cms.available_locales') as $locale)
                                                            @php
                                                                $selected = '';
                                                                if( app()->getLocale() === $locale){
                                                                    $selected = 'selected';
                                                                }
                                                            @endphp
                                                            <option value="{{ $locale }}" {{ $selected }}>@c(['key' => $locale, 'collection' =>false ])</option>
                                                        @endforeach
                                                    </select>
                                                </form>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Title
                                            </td>
                                            <td>
                                                <input class="form-control title">
                                                <input type="hidden" class="file" value="">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Alt
                                            </td>
                                            <td>
                                                <input class="form-control alt">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Tags
                                            </td>
                                            <td>
                                                <input class="form-control tags">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Apply
                                            </td>
                                            <td>
                                                <button class="btn btn-primary image-data apply">Apply</button>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div>


                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="tab-pane fade" id="api" role="tabpanel" aria-labelledby="api-tab">

            </div>

        </div>
    {{-- <div class="grid-masonry" data-masonry='{ "itemSelector": ".grid-item", "columnWidth": 200 }'>
            @foreach( $files as $file)
                @php
                    $url = "/storage/images/" . $file;

                @endphp
                <div class="grid-item">
                    <img style="width:100px;" src="{{ $url }}" alt="" class="img" data-directory="{{ $directory }}" data-file="{{ $file }}" data-url="{{ $url }}"/><br>
                    <input type="checkbox" name=selected_images[] value="{{ $file }}">
                </div>

            @endforeach
        </div> --}}
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
        <div id="mdl_attributes" class="modal" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST" action="/cms/image/attributes">
                    <input type="hidden" name="_method" value="PATCH">
                    <input type="hidden" name="filename" class="filename" value="">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Set Image Attributes</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @csrf
                            <table>
                                <tr>
                                    <td>
                                        Language
                                    </td>
                                    <td>
                                    </td>
                                    <td>
                                         <select name="language">
                                            @foreach( config('cms.available_locales') as $locale)
                                                @php
                                                    $selected = '';
                                                    if( app()->getLocale() === $locale){
                                                        $selected = 'selected';
                                                    }
                                                @endphp
                                                <option value="{{ $locale }}" {{ $selected }}>@c(['key' => $locale, 'default' => $locale ])</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Alt
                                    </td>
                                    <td>
                                    </td>
                                    <td>
                                        <input class="form-control" type="text" name="alt">
                                    </td>
                                    <td>
                                        (will be slugified)
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Title
                                    </td>
                                    <td>
                                    </td>
                                    <td>
                                        <input class="form-control" type="text" name="title">
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Tags
                                    </td>
                                    <td>
                                    </td>
                                    <td>
                                        <input class="form-control" type="text" name="tags">
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
    <script type="text/javascript">
        let actionIds = [];
        let tableResources = new DataTable('#tbl_resources', {
            "columns": [
                {"name": "a", "orderable": false},
                {"name": "b", "orderable": true},
                {"name": "c", "orderable": true},
                {"name": "d", "orderable": true},
                {"name": "e", "orderable": true},
                {"name": "f", "orderable": true},
                {"name": "g", "orderable": true},
                {"name": "h", "orderable": false}
            ],
        });
        let directory = '{{ $directory }}';
        let file = undefined;
        let mdlAttributes = new bootstrap.Modal(document.getElementById("mdl_attributes"), {});

        body.on('click', '#btn_action', function( e){
            e.preventDefault();
            let action = $('#sel_actions').val();
            switch( action){
                case 'delete':
                    deleteSelected( actionIds);
                    break;
            }
        })
        function deleteSelected( ids){
            $.ajax({
                headers : {
                    'X-CSRF-Token' : "{{ csrf_token() }}"
                },
                dataType: "json",
                success : function( data) {
                    window.location.reload()
                },
                error: function (a, b, c){
                    console.log( a)
                },
                data: JSON.stringify( ids),
                url : '/cms/images?ids=' + ids.toString(),
                type : 'DELETE'
            });
        }
        body.on('click', 'input[type=checkbox]', function(){
            let val = this.value
            if( this.checked){
                actionIds.push( this.value)
                $('#btn_action').removeClass('disabled');
            }else{
                actionIds = actionIds.filter( function( obj ) {
                    return obj !== val;
                });
                if( actionIds.length === 0){
                    $('#btn_action').addClass('disabled');
                }
            }
        })
        $('#cb_select_all').on('click', function(){
            let rows = table.rows( {page:'current'} ).data();
            if( $(this).is(':checked') ){
                for( let i = 0; i < rows.length; i++ ){
                    let email = $.parseHTML( rows[i][0])[0].defaultValue;
                    $('*[data-id="' + email + '"]').attr('checked', true);
                }
            } else {
                for( let i = 0; i < rows.length; i++ ){
                    let email = $.parseHTML( rows[i][0])[0].defaultValue;
                    $('*[data-id="' + email + '"]').attr('checked', false);
                }
            }
        })
        body.on('click', '.resource.img', function (e) {
            // let filename = this.dataset.id;
            let src = this.dataset.src
            navigator.clipboard.writeText( src)
                .then(() => {
                    toastr.success("successfully copied");
                })
                .catch(() => {
                    toastr.error("something went wrong");
                });
        });

        body.on('click', '.image.attributes.update', function (e) {
            let filename    = this.dataset.id;
            $('#mdl_attributes .filename').val( filename);
            mdlAttributes.show();
        });


        let aFiles = @json( $accepted_files);
        let acceptedFiles = "";
        for(let x = 0; x < aFiles.length; x++ ){
            if( acceptedFiles.length > 0){
                acceptedFiles += ','
            }
            acceptedFiles += "." + aFiles[x];
        }
        Dropzone.autoDiscover = false;

        var myDropzone = new Dropzone(".dropzone", {
            init: function() {
                myDropzone = this;
                this.on("sending", function(file, xhr, formData){
                    formData.append("data", JSON.stringify( { directory: '{{ $directory}}'}));
                });
                this.on("success", function (file) {
                    window.location.reload();
                });
            },
            autoProcessQueue: false,
            paramName: "files",
            uploadMultiple: true,
            maxFilesize: 100,
            acceptedFiles: acceptedFiles,
        });

        $('#uploadFile').click(function(){
            myDropzone.processQueue();
        });

        $('#selDirectory').on('click', function(){

        })

        $(function () {
            let masonry = $('.grid-masonry').masonry({
                // options...
                itemSelector: '.grid-item',
                columnWidth: 200
            });
        });
    </script>
@endsection
