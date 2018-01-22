@if(!empty($label))
    <div class="form-group">
        {!! Form::label($name, $label, ['class' => 'control-label']) !!}
        {!! Form::select($name, $values, $value, array_merge(['class' => 'form-control'], $attributes)) !!}
    </div>
@else
    {!! Form::select($name, $values, $value, array_merge(['class' => 'form-control'], $attributes)) !!}
@endif