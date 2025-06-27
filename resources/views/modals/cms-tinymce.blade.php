<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/7.6.0/tinymce.min.js"
        integrity="sha512-/4EpSbZW47rO/cUIb0AMRs/xWwE8pyOLf8eiDWQ6sQash5RP1Cl8Zi2aqa4QEufjeqnzTK8CLZWX7J5ZjLcc1Q=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="text/javascript">
    toastr.info('Direct CMS Active: tinymce')
    let app = '{{ config('cms.app')}}'
    $( document ).ready(function() {
        const config = {
            license_key: 'gpl',
            selector: '.cms',
            valid_elements : '*[*]',
            custom_elements: "section[*]",
            menubar: true,
            //  encoding: 'xml',
            // entity_encoding: 'raw',
            // entities: '160,nbsp',
            plugins: [
                'link', 'lists', 'nonbreaking', 'autolink', 'code', "fullscreen"
            ],
            toolbar: [
                'undo redo | bold italic underline | fontfamily fontsize | code',
                'forecolor backcolor | alignleft aligncenter alignright alignfull | numlist bullist outdent indent'
            ],
            // closed: /^(br|hr|input|meta|img|link|param|area|source)$/,
            // valid_styles: {
            //     '*': 'font-size,font-family,color,text-decoration,text-align'
            // },
            //
            // extended_valid_elements:"i[id|class|style],em,br[id|class|style],section[id|class|style]",
            // closed: /^(br|hr|input|meta|img|link|param|area|source)$/,
            // powerpaste_word_import: 'clean',
            // powerpaste_html_import: 'clean',
            setup: function (editor) {
                editor.on('change', function (elm) {

                    // we want the parent element and all the attributes, classes, data, etc.
                    // So we can reconstruct that and upload the complete fragment to the server.
                    let parentClassList = tinymce.activeEditor.selection.getStart().parentNode.offsetParent.classList;
                    let parentDataset = tinymce.activeEditor.selection.getStart().parentNode.offsetParent.dataset;
                    let parentAttributes = tinymce.activeEditor.selection.getStart().parentNode.offsetParent.attributes;
                    let nodeName = tinymce.activeEditor.selection.getStart().parentNode.localName;

                    let content = tinymce.activeEditor.selection.getStart().parentNode.offsetParent.innerHTML;
                    let outerContent = tinymce.activeEditor.selection.getStart().parentNode.offsetParent.outerHTML;
                    let id = tinymce.activeEditor.selection.getStart().parentNode.offsetParent.dataset.cmsId;
                    let mimetype = tinymce.activeEditor.selection.getStart().parentNode.offsetParent.dataset.cmsMimetype;
                    //let id = elm.target.bodyElement.dataset.cmsId;
                    //let mimetype = elm.target.bodyElement.dataset.cmsMimetype;

                    let element = $('<' + nodeName + '></>')
                    for(let x = 0; x < parentClassList.length; x++ ){
                        if( ! parentClassList[x].includes('mce-') ) {
                            $(element).addClass(parentClassList[x]);
                        }
                    }
                    for(let x = 0; x < parentDataset.length; x++ ){
                        if( ! parentDataset[x].includes('mce-') ) {
                            $(element).addAttr( );
                        }
                    }
                    // let content = editor.getContent()
                    console.log('Editor content changed:', content);
                    //let id = elm.target.bodyElement.dataset.cmsId;
                    //let mimetype = elm.target.bodyElement.dataset.cmsMimetype;
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
                });
            }
        };
        tinymce.init( config);
    });
</script>
