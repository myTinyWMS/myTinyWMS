@if(!empty($label))
<div class="form-group">
    {!! Form::label($name, $label, ['class' => 'form-label']) !!}
    {!! Form::text($name, $value, array_merge(['class' => 'form-input placeholder-gray-600'], $attributes)) !!}
</div>
@else
    {!! Form::text($name, $value, array_merge(['class' => 'form-input placeholder-gray-600'], $attributes)) !!}
@endif