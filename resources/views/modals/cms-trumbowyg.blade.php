<script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/trumbowyg.min.js" integrity="sha512-YJgZG+6o3xSc0k5wv774GS+W1gx0vuSI/kr0E0UylL/Qg/noNspPtYwHPN9q6n59CTR/uhgXfjDXLTRI+uIryg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="text/javascript">
    toastr.info('Direct CMS Active')
    let app = '{{ config('cms.app')}}'
    var timeoutId = 0;

    $(document).ready(function() {
        var editor = $('.cms').trumbowyg({
            autogrow: true,
            removeformatPasted: true,
            btns: [
                ['undo', 'redo'], // Only supported in Blink browsers
                ['formatting'],
                ['strong', 'em', 'del'],
                ['superscript', 'subscript'],
                ['link'],
                ['insertImage'],
                ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                ['unorderedList', 'orderedList'],
                ['horizontalRule'],
                ['removeformat'],
                ['fullscreen']
            ]
        })
        editor.on('tbwblur', function(e) {
            //clearTimeout(timeoutId); // doesn't matter if it's 0
            //timeoutId = setTimeout( function(){
                let id = e.currentTarget.dataset.cmsId;
                let mimetype = e.currentTarget.dataset.cmsMimetype;
                let newContent = e.currentTarget.innerHTML;

                let classList = e.currentTarget.classList;
                let dataset = e.currentTarget.dataset;
                let attributes = e.currentTarget.attributes;
                let outerContent = e.currentTarget.outerHTML;
                let element = $(outerContent);
                $( element).removeClass( "cms");
                $( element).removeClass( "trumbowyg-editor");
                $( element).removeAttr( "data-cms-id");
                $( element).removeAttr( "data-cms-mimetype");
                $( element).removeAttr( "contenteditable");
                $( element).removeAttr( "dir");
                if ( ! $( element).prop('classList').length) $( element).removeAttr('class');

                let content = $( element).prop( 'outerHTML');
                console.log( content)
                let data = {
                    "_token" : "{{ csrf_token() }}",
                    'value':  content,
                    'mimetype': mimetype
                }
                fetch('/cms/tag/direct/' + app + '/' + id, {
                    headers: {
                        "X-CSRF-TOKEN" : "{{ csrf_token() }}",
                        'Content-Type': 'application/json'
                    },
                    method: 'PATCH',
                    body: JSON.stringify( data)
                })
                    .then(response => {
                        return response.text();
                    })
                    .then(data => {
                        if( data === 'OK') {
                            toastr.success( data, "Content");
                        }else{
                            console.log( data)
                            toastr.error( "En error occurred during saving");
                        }
                    })
                    .catch(error => {
                        console.error( error.message)
                        toastr.error( error.message, "Content");
                    });

            //}, 1000, e);

        })
    })
</script>
