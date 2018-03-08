<?php

namespace Mss\Providers;

use Collective\Html\FormFacade as Form;
use Illuminate\Support\ServiceProvider;

class FormComponentsServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot() {
        Form::component('bsText', 'components.form.text', ['name', 'value' => null, 'attributes' => [], 'label' => null]);
        Form::component('bsPassword', 'components.form.password', ['name', 'attributes' => [], 'label' => null, 'helpText' => null]);
        Form::component('aceHTML', 'components.form.ace_html', ['name', 'value' => null, 'attributes' => [], 'label' => null]);
        Form::component('summernote', 'components.form.summernote', ['name', 'value' => null, 'attributes' => [], 'label' => null]);
        Form::component('bsRoleSelect', 'components.form.select_role', ['name', 'values' => collect([]), 'attributes' => [], 'label' => null]);
        Form::component('bsCheckbox', 'components.form.checkbox', ['name', 'value' => null, 'label' => null, 'checked' => false, 'attributes' => []]);
        Form::component('bsFile', 'components.form.file', ['name', 'label' => '', 'helpBlock' => '', 'attributes' => []]);
        Form::component('bsSelect', 'components.form.select', ['name', 'value' => null, 'values' => collect([]), 'label' => '', 'attributes' => []]);
        Form::component('bsTextarea', 'components.form.textarea', ['name', 'value' => null, 'attributes' => [], 'label' => null]);
    }

    /**
     * @return void
     */
    public function register()
    {
        //
    }
}
