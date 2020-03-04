<template>
    <modal name="selectOrderArticleModal" height="auto" width="95%" :scrollable="true" classes="modal" :clickToClose="true" @opened="opened">
        <h4 class="modal-title font-bold text-xl">{{ $t('Artikel auswählen') }}</h4>

        <div class="row">
            <div class="w-full">
                <slot></slot>
            </div>
        </div>
    </modal>

</template>

<script>
    import { serverBus } from '../app';

    export default {
        data() {
            return {
                notExistingIds: null
            }
        },

        created() {
            serverBus.$on('filterOrderArticleList', (notExistingIds) => {
                this.notExistingIds = notExistingIds;
            });

        },

        methods: {
            opened () {
                window.LaravelDataTables[window.DataTableId]=$('#'+window.DataTableId).DataTable(window.DataTableConfig);
                window.LaravelDataTables[window.DataTableId].columns(17).search(this.notExistingIds).draw();
            }
        }
    }
</script>