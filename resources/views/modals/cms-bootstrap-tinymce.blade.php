{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/7.6.0/tinymce.min.js"
        integrity="sha512-/4EpSbZW47rO/cUIb0AMRs/xWwE8pyOLf8eiDWQ6sQash5RP1Cl8Zi2aqa4QEufjeqnzTK8CLZWX7J5ZjLcc1Q=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/@tinymce/tinymce-jquery@2/dist/tinymce-jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/7.6.0/plugins/image/plugin.min.js"></script> --}}
<div id="mdl_text_html" class="modal modal-lg" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">key: Language: Page:</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" class="id">
                <input type="hidden" class="mimetype">
                <div id="text_html"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary save_text_html">Save changes</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    toastr.info('Direct CMS Active')

    let app = '{{ config('cms.app')}}'
    let body = $('body');
    var mdlTextHtml = new bootstrap.Modal(document.getElementById("mdl_text_html"), {});

    function saveContent( id, value, mimetype) {
        console.log("saving...")
        console.log(value)
        let data = {
            "_token": "{{ csrf_token() }}",
            'value': value,
            'mimetype': mimetype
        }
        fetch('/cms/tag/direct/' + app + '/' + id, {
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                'Content-Type': 'application/json'
            },
            method: 'PATCH',
            body: JSON.stringify(data)
        })
            .then(response => {
                return response.text();
            })
            .then(data => {
                if (data === 'OK') {
                    location.reload();
                } else {
                    console.log(data)
                    toastr.error("En error occurred during saving");
                }
            })
            .catch(error => {
                console.error(error.message)
                toastr.error(error.message, "Content");
            });
    }
    body.on('dblclick', '.cms', function( e){

        let outerContent = this.outerHTML;
        let id = this.dataset.cmsId;
        let mimetype = this.dataset.cmsMimetype;

        let element = $(outerContent);
        $( element).removeClass( "cms");
        $( element).removeClass( "trumbowyg-editor");
        $( element).removeAttr( "data-cms-id");
        $( element).removeAttr( "data-cms-mimetype");
        if ( ! $( element).prop('classList').length) $( element).removeAttr('class');

        let content = $( element).prop( 'outerHTML');

        $("#mdl_text_html .id" ).val( id);
        $("#mdl_text_html .mimetype" ).val( mimetype);
        tinymce.get('text_html').setContent(content);
        mdlTextHtml.show();
    })

    body.on('click', '.save_text_html', function( e){
        let content = tinymce.get('text_html').getContent();
        let id = $("#mdl_text_html .id" ).val()
        let mimetype = $("#mdl_text_html .mimetype" ).val()
        saveContent( id, content, mimetype)
    })

    $('#text_html').tinymce({
        height: 500,
        license_key: 'gpl',
        selector: '.cms',  // change this value according to your HTML
        valid_elements : '*[*]',
        extended_valid_elements:"i[id|class|style],em,br[id|class|style],section[id|class|style],span[id|class|style]",
        encoding: 'xml',
        entity_encoding: 'raw',
        entities: '160,nbsp',
        convert_urls:false,

        save_onsavecallback: function (editor) {
            let content = editor.getContent()
            let id = $("#mdl_text_html .id" ).val()
            let mimetype = $("#mdl_text_html .mimetype" ).val()
            saveContent( id, content, mimetype)
        },
        plugins: [
            'link', 'lists', 'nonbreaking', 'autolink', 'code', 'image'
        ],toolbar: [
            'undo redo | bold italic underline | fontfamily fontsize | image | code',
            'forecolor backcolor | alignleft aligncenter alignright alignfull | numlist bullist outdent indent'
        ]
    });
    document.addEventListener('focusin', function (e) {
        if (e.target.closest('.tox-tinymce-aux, .moxman-window, .tam-assetmanager-root') !== null) {
            e.stopImmediatePropagation();
        }
    });
</script>
