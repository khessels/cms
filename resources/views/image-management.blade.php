@extends('package-views::layouts.cms')

@section('head-title')
    @c(['key' => 'title', 'page' => $page])
@endsection

@section('main')
{{--    <style>--}}
{{--        .grid-sizer,--}}
{{--        .grid-item { width: 20%; }--}}
{{--        /* 2 columns */--}}
{{--        .grid-item--width2 { width: 40%; }--}}
{{--    </style>--}}
    <div class="section" style="margin: 20px;">
        <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                    <form action="{{ route('cms.dropzone.store') }}" method="post" enctype="multipart/form-data" id="image-upload" class="dropzone">
                        <input type="hidden" name="directory" value="{{ $directory }}">
                        @csrf
                    </form>
                    <button id="uploadFile" class="btn btn-primary mt-1">Upload Images</button>
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
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
                                            @foreach( config('app.available_locales') as $locale)
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
{{--                        <table class="datatable">--}}
{{--                            <thead>--}}
{{--                                <tr>--}}
{{--                                    <th>Language</th>--}}
{{--                                    <th>File</th>--}}
{{--                                    <th>Title</th>--}}
{{--                                    <th>Alt</th>--}}
{{--                                </tr>--}}
{{--                            </thead>--}}
{{--                        </table>--}}

                    </div>
                </div>
            </div>
        </div>
        <div>
            <form action="/cms/images/action" method="POST">
                <input type="hidden" name="directory" value="{{ $directory }}">
                @csrf
                <input type="hidden" name="language" value="{{ app()->getLocale() }}">
                <label>With Selected</label>
                <select class="form-select" id="selWithSelected" name="action">
                    <option value="">Select</option>
                    <option value="delete">Delete</option>
                    <option value="move">Move</option>
                </select>
                <select class="form-select" name="moveto" id="moveto" style="display: none;">
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
                <input type="submit" class="btn btn-primary" value="Apply">
                <div class="grid-masonry" data-masonry='{ "itemSelector": ".grid-item", "columnWidth": 200 }'>
                    @foreach( $files as $file)
                        @php
                            $url = "/storage/" . $file;

                        @endphp
                        <div class="grid-item">
                            <img style="width:100px;" src="{{ $url }}" alt="" class="img" data-directory="{{ $directory }}" data-file="{{ $file }}" data-url="{{ $url }}"/><br>
{{--                            <label>{{ $url }}</label><br>--}}
                            <input type="checkbox" name=selected_images[] value="{{ $file }}">
                        </div>

                    @endforeach
                </div>
            </form>
        </div>
    </div>
    <script type="text/javascript">
        let directory = '{{ $directory }}';
        let file = undefined;

        $('.img').on('click', function (e) {
            console.log(this.dataset.file);
            file = this.dataset.file;
            let language = $('#language').val();
            $.ajax({
                headers : {
                    'X-CSRF-Token' : "{{ csrf_token() }}"
                },
                success : function( data) {
                    $('#image-properties .title').val( '');
                    $('#image-properties .alt').val( '');
                    $('#image-properties .tags').val( '');

                    if( data){
                        $('#image-properties .title').val( data.title);
                        $('#image-properties .alt').val( data.alt);
                        $('#image-properties .tags').val( data.tags);
                    }
                },
                error: function (a, b, c){
                    console.log( a)
                },
                data:{ language: language, file: file},
                url : '/cms/image/data',
                type : 'GET'
            });
        })
        $('.image-data.apply').on('click', function (e) {
            let title = $('#image-properties .title').val();
            let alt = $('#image-properties .alt').val();
            let tags = $('#image-properties .tags').val();
            let language = $('#language').val();
            $.ajax({
                headers : {
                    'X-CSRF-Token' : "{{ csrf_token() }}"
                },
                success : function( data) {
                    if( data.length > 0){
                    }
                },
                error: function (a, b, c){
                    console.log( a)
                },
                data: {
                    _token: "{{ csrf_token() }}",
                    properties:{ title: title, alt: alt, tags: tags, language: language, file: file }
                },
                url : '/cms/image/data',
                type : 'POST'
            });
        })
        $('#selWithSelected').on('change', function(){
            if(this.value === 'move'){
                $(' #moveto').show();
            }else{
                $(' #moveto').hide();
            }
        })
        let aFiles = @json( $accepted_files);
        let acceptedFiles = "";
        for(let x = 0; x < aFiles.length; x++ ){
            if( acceptedFiles.length > 0){
                acceptedFiles += ','
            }
            acceptedFiles += "." + aFiles[x];
        }
        Dropzone.autoDiscover = false;
        var images = {{ Js::from( $files) }};
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



