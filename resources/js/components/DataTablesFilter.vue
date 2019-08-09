<template>
    <dropdown class="hidden">
        <template v-slot:trigger>
            <div class="shadow btn btn-white rounded-lg flex text-base h-10 rounded-tr-none rounded-br-none border-r">
                <div class="px-2">{{ $t('Filter') }}</div>
                <z icon="cheveron-down" class="fill-current w-4 h-4 mt-1"></z>
            </div>
        </template>

        <template v-slot:content>
            <slot></slot>
        </template>
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