<template>
    <modal name="assignOrderMessageModal" height="auto" width="95%" :scrollable="true" classes="modal" :clickToClose="false" @before-open="beforeOpen" @opened="opened">
        <h4 class="modal-title font-bold text-xl">Nachricht zuordnen</h4>

        <form method="post" :action="route('order.message_assign')">
            <div class="row">
                <div class="w-full">
                    <slot></slot>
                </div>
            </div>

            <div class="modal-footer">
                <input type="hidden" name="_token" :value="token">
                <input type="hidden" name="message" :value="message_id">
                <button type="button" class="btn btn-default" @click="$modal.hide('assignOrderMessageModal')">Abbrechen</button>
                <button type="submit" class="btn btn-primary" id="save-assign-order-message">Speichern</button>
            </div>
        </form>
    </modal>

</template>

<script>
    export default {
        data() {
            return {
                message_id: null
            }
        },

        computed: {
            token: function () {
                return window.axios.defaults.headers.common['X-CSRF-TOKEN'];
            }
        },

        methods: {
            opened () {
                window.LaravelDataTables[window.DataTableId]=$('#'+window.DataTableId).DataTable(window.DataTableConfig);
            },
            beforeOpen (event) {
                this.message_id = event.params.message_id;
            }
        }
    }
</script>