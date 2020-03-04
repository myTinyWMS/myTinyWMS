<template>
    <vue-datepicker-local rangeSeparator="-" v-model="localValue" :local="local" :format="format" :type="type" input-class="form-input"/>
</template>

<script>
    // https://vuejsexamples.com/a-beautiful-datepicker-component-for-vue2/
    import VueDatepickerLocal from 'vue-datepicker-local'

    export default {
        props: {value: {}, format: {default: 'DD.MM.YYYY'}, outputformat: {default: 'YYYY-MM-DD'}, type: {default: 'normal'}},
        components: {
            VueDatepickerLocal
        },
        computed: {
            localValue: {
                get () {
                    return this.value
                },
                set (value) {
                    if (typeof value == 'object' && !(value instanceof Date)) {
                        value[0] = moment(value[0]).format(this.outputformat);
                        value[1] = moment(value[1]).format(this.outputformat);
                        this.$emit('input', value);
                    } else {
                        this.$emit('input', moment(value).format(this.outputformat))
                    }
                }
            }
        },
        data () {
            return {
                local: {
                    dow: 1, // Sunday is the first day of the week
                    // hourTip: 'Select Hour', // tip of select hour
                    // minuteTip: 'Select Minute', // tip of select minute
                    // secondTip: 'Select Second', // tip of select second
                    yearSuffix: '', // suffix of head year
                    monthsHead: [this.$t('Januar'), this.$t('Februar'), this.$t('März'), this.$t('April'), this.$t('Mai'), this.$t('Juni'), this.$t('Juli'), this.$t('August'), this.$t('September'), this.$t('Oktober'), this.$t('November'), this.$t('Dezember')], // months of head
                    months: [this.$t('Jan'), this.$t('Jan'), this.$t('Feb'), this.$t('Mär'), this.$t('Apr'), this.$t('Mai'), this.$t('Jun'), this.$t('Jul'), this.$t('Aug'), this.$t('Sep'), this.$t('Okt'), this.$t('Nov'), this.$t('Dez')], // months of panel
                    weeks: [this.$t('Mo'), this.$t('Di'), this.$t('Mi'), this.$t('Do'), this.$t('Fr'), this.$t('Sa'), this.$t('So')], // weeks,
                    cancelTip: this.$t('abbrechen'),
                    submitTip: this.$t('bestätigen')
                }
            }
        }
    }
</script>