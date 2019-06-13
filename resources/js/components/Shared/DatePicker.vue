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
                    if (typeof value == 'object') {
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
                    monthsHead: 'Januar_Februar_März_April_Mai_Juni_Juli_August_September_Oktober_November_Dezember'.split('_'), // months of head
                    months: 'Jan_Feb_Mär_Apr_Mai_Jun_Jul_Aug_Sep_Okt_Nov_Dez'.split('_'), // months of panel
                    weeks: 'Mo_Di_Mi_Do_Fr_Sa_So'.split('_'), // weeks,
                    cancelTip: 'abbrechen',
                    submitTip: 'bestätigen'
                }
            }
        }
    }
</script>