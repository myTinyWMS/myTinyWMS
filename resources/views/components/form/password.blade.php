@if(!empty($label))
    <div class="form-group">
        {!! Form::label($name, $label, ['class' => 'form-label']) !!}
        {!! Form::password($name, array_merge(['class' => 'form-input'], $attributes)) !!}
    </div>
@else
    {!! Form::password($name, $value, array_merge(['class' => 'form-input'], $attributes)) !!}
@endif