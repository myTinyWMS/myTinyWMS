<div class="form-group">
    {!! Form::label($name, $label, ['class' => 'col-lg-1 control-label']) !!}
    <div class="col-lg-11">
        <div id="editor">{{ $value }}</div>
    </div>
    {!! Form::textarea($name, $value, ['class' => 'hidden']) !!}
</div>

@section('extra_head')
    <style>
        #editor {
            height: 800px;
            width: 100%;
        }
    </style>
@endsection
@push('scripts')
    <script src="{{ asset('js/ace/ace.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/ace/mode-html.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/ace/ext-beautify.js') }}" type="text/javascript"></script>
    <script>
        var editor = ace.edit("editor");
        editor.setTheme("ace/theme/chrome");
        var HTMLMode = ace.require("ace/mode/html").Mode;
        editor.session.setMode(new HTMLMode());

        var textarea = $('textarea[name="{{ $name }}"]');
        editor.getSession().on("change", function () {
            textarea.val(editor.getSession().getValue());
        });
    </script>
@endpush