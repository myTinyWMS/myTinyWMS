<div class="form-group">
    {!! Form::label($name, $label, ['class' => 'form-label']) !!}
    <wysiwyg-editor name="{{ $name }}" content="{{ $value }}" show-signature-button="@yield('summernote_show_signature_button')" signature="@yield('summernote_signature')"></wysiwyg-editor>
</div>