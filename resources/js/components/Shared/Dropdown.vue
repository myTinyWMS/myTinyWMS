<template>
    <div class="dropdown-button group" ref="dropdownMenu">
        <div class="dropdown-button-header" @click="isVisible = ! isVisible">
            <slot name="trigger"></slot>
        </div>
        <div class="dropdown-button-items flex flex-col" :class="direction + '-0'" v-show="isVisible">
            <slot name="content"></slot>
        </div>
    </div>
</template>

<script>
    export default {
        props:{
            direction: {
                default: 'left'
            }
        },

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
            let that = this;
            document.addEventListener('click', this.documentClick);

            this.$root.$on('close-dropdown', (text) => {
                that.isVisible = false;
            })
        },

        destroyed () {
            // important to clean up!!
            document.removeEventListener('click', this.documentClick)
        }
    }
</script>