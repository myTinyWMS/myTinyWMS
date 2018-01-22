<div class="checkbox checkbox-primary">
    {!! Form::checkbox($name, $value, $checked, array_merge(['id' => $name], $attributes)) !!}
    <label for="{{ $name }}">
        {{ $label }}
    </label>
</div>