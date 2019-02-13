@if(!empty($label))
    <div class="form-group">
        {!! Form::label($name, $label, ['class' => 'form-label']) !!}
        {!! Form::select($name, $values, $value, array_merge(['class' => 'form-select'], $attributes)) !!}
    </div>
@else
    {!! Form::select($name, $values, $value, array_merge(['class' => 'form-select'], $attributes)) !!}
@endif