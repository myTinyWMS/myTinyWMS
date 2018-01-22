<div class="form-group">
    {!! Form::label($name, $label, ['class' => 'control-label']) !!}
    {!! Form::password($name, array_merge(['class' => 'form-control'], $attributes)) !!}
    <span class="help-block m-b-none">{{ $helpText }}</span>
</div>