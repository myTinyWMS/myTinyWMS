<div class="form-group">
    {!! Form::label('roles', __('Rollen'), ['class' => 'control-label']) !!}
    {!! Form::select('roles[]', App\Models\Role::all()->pluck('display_name', 'id'), $values->pluck('id'), ['multiple' => 'multiple', 'placeholder' => __('Bitte wählen'), 'class' => 'form-control']) !!}
</div>