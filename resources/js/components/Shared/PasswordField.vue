<template>
    <div class="form-group">
        <div class="flex">
            <label :for="id" class="form-label">{{ label }}</label>
            <i class="fa fa-eye-slash text-sm text-gray-600 ml-4 cursor-pointer" :title="$t('Passwort anzeigen')" v-if="fieldType == 'password'" @click="toggleFieldType"></i>
            <i class="fa fa-eye text-sm text-gray-600 ml-4" :title="$t('Passwort verbergen')" v-if="fieldType == 'text'" @click="toggleFieldType"></i>
        </div>
        <input :type="fieldType" class="form-input" :name="name" :id="id" :value="value" />
    </div>
</template>

<script>
    export default {
        props: ['id', 'name', 'label', 'value'],

        data() {
            return {
                fieldType: 'password'
            }
        },

        methods: {
            toggleFieldType() {
                this.fieldType = (this.fieldType == 'password') ? 'text' : 'password';
            }
        }
    }
</script>