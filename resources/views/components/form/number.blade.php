@if(!empty($label))
<div class="form-group">
    {!! Form::label($name, $label, ['class' => 'control-label']) !!}
    {!! Form::number($name, $value, array_merge(['class' => 'form-control'], $attributes)) !!}
</div>
@else
    {!! Form::number($name, $value, array_merge(['class' => 'form-control'], $attributes)) !!}
@endif