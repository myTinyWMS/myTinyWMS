<template>
    <dropdown caption="Filter">
        <slot></slot>
    </dropdown>
</template>

<script>
    export default {
        mounted() {
            var that = this;

            $('#dataTableBuilder').on('init.dt', function () {
                document.getElementById('table-filter').appendChild(that.$el);
            });
        }
    }
</script>