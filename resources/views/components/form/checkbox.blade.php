<div class="i-checks {{ $parentClasses }}">
    <label>
        {!! Form::checkbox($name, $value, $checked, array_merge(['id' => $name], $attributes)) !!}
        {{ $label }}
    </label>
</div>