@extends('package-views::layouts.cms')


@section('css')
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>
    <script>
      tinymce.init({
        selector: '#mytextarea'
      });
    </script>
@endsection

@section('main')
    <div class="container">
        <h4>content editor: {{ $content }}</h4>
        <form method="post">
            <textarea id="mytextarea">Hello, World!</textarea>
        </form>
    </div>
@endsection

@section('footer-js')
    <script>
        let app = '{{ config('cms.app')}}'

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
