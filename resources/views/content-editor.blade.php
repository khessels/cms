@extends('package-views::layouts.cms')


@section('css')
    <!-- Place the first <script> tag in your HTML's <head> -->
    <script src="https://cdn.tiny.cloud/1/qwhw6bty2u7h1kqxkp3fquyqu7hgmv5cf3e4axm74dfkcrwp/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>

@endsection

@section('main')
    <div class="container">
        <h4>content editor: {{ $content }}</h4>
        <form method="post">
            <textarea>
  Welcome to TinyMCE!
</textarea>
        </form>
    </div>
@endsection

@section('footer-js')
    <script>
        let app = '{{ config('cms.app')}}'

        tinymce.init({
            selector: 'textarea',
            plugins: [
            // Core editing features
            'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'link', 'lists', 'media', 'searchreplace', 'table', 'visualblocks', 'wordcount',
            // Your account includes a free trial of TinyMCE premium features
            // Try the most popular premium features until Oct 4, 2025:
            'checklist', 'mediaembed', 'casechange', 'formatpainter', 'pageembed', 'a11ychecker', 'tinymcespellchecker', 'permanentpen', 'powerpaste', 'advtable', 'advcode', 'advtemplate', 'ai', 'uploadcare', 'mentions', 'tinycomments', 'tableofcontents', 'footnotes', 'mergetags', 'autocorrect', 'typography', 'inlinecss', 'markdown','importword', 'exportword', 'exportpdf'
            ],
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography uploadcare | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
            tinycomments_mode: 'embedded',
            tinycomments_author: 'Author name',
            mergetags_list: [
            { value: 'First.Name', title: 'First Name' },
            { value: 'Email', title: 'Email' },
            ],
            ai_request: (request, respondWith) => respondWith.string(() => Promise.reject('See docs to implement AI Assistant')),
            uploadcare_public_key: '93bdd3ccf31939814c86',
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
