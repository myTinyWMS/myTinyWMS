<div class="form-group">
    {!! Form::label('roles', __('form.select_roles'), ['class' => 'control-label']) !!}
    {!! Form::select('roles[]', App\Models\Role::all()->pluck('display_name', 'id'), $values->pluck('id'), ['multiple' => 'multiple', 'placeholder' => 'Please select', 'class' => 'form-control']) !!}
</div>