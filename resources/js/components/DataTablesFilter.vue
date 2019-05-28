<template>
    <dropdown caption="Filter" class="hidden">
        <slot></slot>
    </dropdown>
</template>

<script>
    export default {
        mounted() {
            var that = this;

            $('#dataTableBuilder').on('init.dt', function () {
                document.getElementById('table-filter').appendChild(that.$el);
                document.getElementById('table-filter').getElementsByClassName('dropdown-button')[0].classList.remove('hidden');
                document.getElementById('table-filter').parentElement.classList.add('has-filter');
            });
        }
    }
</script>