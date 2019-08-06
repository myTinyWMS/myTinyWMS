<template>
    <dropdown caption="Filter" class="hidden">
        <slot></slot>
    </dropdown>
</template>

<script>
    export default {
        mounted() {
            var that = this;

            if ($('#dataTableBuilder').length) {
                $('#dataTableBuilder').on('init.dt', function () {
                    $('body').trigger('datatablesInit');
                });
            }

            $('body').on('datatablesInit', function () {
                document.getElementById('table-filter').appendChild(that.$el);
                document.getElementById('table-filter').getElementsByClassName('dropdown-button')[0].classList.remove('hidden');
                document.getElementById('table-filter').parentElement.classList.add('has-filter');
            });
        }
    }
</script>