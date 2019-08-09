<template>
    <div class="flex flex-col">
        <label>{{ label }}:</label>
        <select @change="onChange()" class="form-control input-sm" v-model="value" :id="id">
            <slot></slot>
        </select>
    </div>
</template>

<script>
    export default {
        props: ["label", "colId", "preSet", "isArticleCategoryCol", "id"],

        data() {
            return {
                value: this.preSet,
            }
        },

        methods: {
            onChange() {
                window.LaravelDataTables.dataTableBuilder.columns(this.colId).search(this.value).draw();

                if (this.isArticleCategoryCol && this.colId == 16) {
                    if (this.value > 0) {
                        window.LaravelDataTables.dataTableBuilder.columns(1).visible(true);
                        $('#dataTableBuilder thead th:eq(1)').click();
                    } else {
                        window.LaravelDataTables.dataTableBuilder.columns(1).visible(false);
                    }
                }

                this.$root.$emit('close-dropdown', 'true')
            }
        },
    }
</script>