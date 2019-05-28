<template>
    <div class="dropdown-button group" ref="dropdownMenu">
        <div class="dropdown-button-header shadow btn btn-white rounded-lg flex text-base py-4 h-10 rounded-tr-none rounded-br-none border-r" @click="isVisible = ! isVisible">
            <div class="px-2">{{ caption }}</div>
            <z icon="cheveron-down" class="fill-current w-4 h-4"></z>
        </div>
        <div class="dropdown-button-items left-0 flex flex-col" v-show="isVisible">
            <slot></slot>
        </div>
    </div>
</template>

<script>
    export default {
        props: ["content", "caption"],

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