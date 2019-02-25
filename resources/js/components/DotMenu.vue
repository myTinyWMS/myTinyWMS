<template>
    <div class="dropdown-button group" ref="dropdownMenu">
        <div class="dropdown-button-header">
            <i class="fa fa-ellipsis-h text-grey-dark" @click="isVisible = ! isVisible"></i>
        </div>
        <div class="dropdown-button-items" v-if="isVisible">
            <slot></slot>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                isVisible: false
            }
        },

        methods: {
            documentClick(e) {
                var el = this.$refs.dropdownMenu;
                var target = e.target;
                if (el !== target && !el.contains(target)) {
                    this.isVisible = false;
                }
            }
        },

        created () {
            document.addEventListener('click', this.documentClick)
        },

        destroyed () {
            // important to clean up!!
            document.removeEventListener('click', this.documentClick)
        }
    }
</script>