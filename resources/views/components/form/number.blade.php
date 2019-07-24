@if(!empty($label))
<div class="form-group">
    {!! Form::label($name, $label, ['class' => 'control-label']) !!}
    {!! Form::number($name, $value, array_merge(['class' => 'form-input'], $attributes)) !!}
</div>
@else
    {!! Form::number($name, $value, array_merge(['class' => 'form-input'], $attributes)) !!}
@endif