<div class="form-group">
    {!! Form::label($name, $label, ['class' => 'control-label']) !!}
    {!! Form::textarea($name, $value, ['class' => 'summernote']) !!}
</div>

@push('scripts')
    <script>
        $(document).ready(function () {
            $('.summernote').summernote({
                lang: 'de-DE',
                height: 500,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['table', ['table']],
                    ['help', ['help']]
                ]
            });
        });
    </script>
@endpush