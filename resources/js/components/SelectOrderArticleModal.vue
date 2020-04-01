<template>
    <modal name="selectOrderArticleModal" height="auto" width="95%" :scrollable="true" classes="modal bg-gray-200" :clickToClose="true" @opened="opened">
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
                if (this.notExistingIds && this.notExistingIds.length > 0) {
                    window.LaravelDataTables[window.DataTableId].columns(17).search(this.notExistingIds).draw();
                } else {
                    window.LaravelDataTables[window.DataTableId].draw();
                }
            }
        }
    }
</script>