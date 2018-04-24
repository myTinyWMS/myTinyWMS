<div class="form-group">
    {!! Form::label($name, $label, ['class' => 'control-label']) !!}
    {!! Form::textarea($name, $value, ['class' => 'summernote']) !!}
</div>

@push('scripts')
    <script>
        @yield('summernote_custom_js')
        $(document).ready(function () {
            $('.summernote').summernote({
                lang: 'de-DE',
                height: 800,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['table', ['table']],
                    @yield('summernote_custom_toolbar')
                    /*['insert', ['link', 'picture', 'hr']],
                    ['view', ['fullscreen', 'codeview']],*/
                    ['help', ['help']]
                ]
                @yield('summernote_custom_config')
            });
        });
    </script>
@endpush