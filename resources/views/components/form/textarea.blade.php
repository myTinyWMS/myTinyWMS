@if(!empty($label))
<div class="form-group">
    {!! Form::label($name, $label, ['class' => 'form-label']) !!}
    {!! Form::textarea($name, $value, array_merge(['class' => 'form-textarea'], $attributes)) !!}
</div>
@else
    {!! Form::textarea($name, $value, array_merge(['class' => 'form-textarea'], $attributes)) !!}
@endif