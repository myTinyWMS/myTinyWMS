<div class="checkbox checkbox-primary {{ $parentClasses }}">
    {!! Form::checkbox($name, $value, $checked, array_merge(['id' => $name], $attributes)) !!}
    <label for="{{ $name }}">
        {{ $label }}
    </label>
</div>