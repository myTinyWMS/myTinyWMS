@if(!empty($label))
<div class="form-group">
    {!! Form::label($name, $label, ['class' => 'control-label']) !!}
    {!! Form::text($name, $value, array_merge(['class' => 'form-control'], $attributes)) !!}
</div>
@else
    {!! Form::text($name, $value, array_merge(['class' => 'form-control'], $attributes)) !!}
@endif