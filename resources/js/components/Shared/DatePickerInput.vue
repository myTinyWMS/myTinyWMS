<template>
    <div>
        <input type="hidden" :name="name" v-model="localValue">
        <date-picker v-model="localValue" :format="format" :outputformat="outputformat" :type="type" :class="pickerClass"></date-picker>
    </div>
</template>

<script>
    export default {
        props: {outputformat: {}, format: {}, type: {}, name: {}, pickerClass: {}, default: {default: null}},

        data () {
            return {
                localValue: this.default
            }
        }
    }
</script>