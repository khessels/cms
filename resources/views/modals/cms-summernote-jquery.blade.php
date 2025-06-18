<script src="/assets/summernote-0.9.0-dist/summernote-bs5.min.js"></script>

<script type="text/javascript">
    toastr.info('Direct CMS Active')
    let app = '{{ config('kcs-content-manager.app')}}'
    toastr.info( app)
    $(document).ready(function() {
        $('.cms').summernote();
    });
</script>
