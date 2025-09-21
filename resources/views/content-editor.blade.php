@extends('parent::components.layouts.public')

@section('head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.6/skins/content/default/content.min.css" integrity="sha512-Y464WrZHfkj3DZi0sHBxavSNE6iKAo4zFn1hQsFx9iv/mMgXZcxesvDBUyQWzRo6T8P/C0twVR+cY3wzKH5jCg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.6/tinymce.min.js" integrity="sha512-DhgyMP4Cd1jgUhBem6TDsFEzOk4SnSpLAxADwbh2p/bejweunVCRr5UuBEPgtG5J0zlOvijajXaGHwP6B+iywg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

@endsection

@section('menu')
    <div class="container">
        @foreach( $content as $c)
            <br>
            <h4>language: {{ $c[ 'language'] }}</h4>
            <form method="post">
                <textarea>{{ $c[ 'content'] }}</textarea><br>
                <input type="submit" class='btn btn-primary'>
            </form>
            <br><br><br>
            <hr>
        @endforeach
    </div>
<script>
        let app = '{{ config('cms.app')}}'

        tinymce.init({
            selector: 'textarea',
            plugins: [
            // Core editing features
            'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'link', 'lists', 'media', 'searchreplace', 'table', 'visualblocks', 'wordcount', 'code',
            // Your account includes a free trial of TinyMCE premium features
            // Try the most popular premium features until Oct 4, 2025:
            'checklist', 'mediaembed', 'casechange', 'formatpainter', 'pageembed', 'a11ychecker', 'tinymcespellchecker', 'permanentpen', 'powerpaste', 'advtable', 'advcode', 'advtemplate', 'ai', 'uploadcare', 'mentions', 'tinycomments', 'tableofcontents', 'footnotes', 'mergetags', 'autocorrect', 'typography', 'inlinecss', 'markdown','importword', 'exportword', 'exportpdf'
            ],
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography uploadcare | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat | code',
        });

        body.on( 'click', '.btn.test.communication', function( e){
            e.preventDefault();
            $.ajax({
                url: '/cms/test/communication',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                },
                success: function( response){
                    if( response.status == 200){
                        $('.result.test.communication').html( response.data)
                        toastr.success( response.data);
                    }else if( response.status == 300){
                        $('.result.test.communication').html( response.data)
                        toastr.warning( response.data);
                    }else if( response.status == 400){
                        $('.result.test.communication').html( response.data)
                        toastr.error( response.data);
                    }else{
                        $('.result.test.communication').html( response.data)
                        toastr.error( response.data);
                    }
                },
                error: function( response){
                    toastr.error('Error communicating with server');
                }
            });
        });

    </script>
    <script type="text/javascript">

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

    </script>
@endsection

@section('left-sidebar')
@endsection

@section('right-sidebar')
@endsection

@section('body')
@endsection
