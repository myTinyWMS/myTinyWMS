<template>
    <div class="dropdown-button group cursor-pointer" ref="dropdownMenu">
        <div class="dropdown-button-header text-sm text-blue-500 rounded-t-lg">
            <i class="fa fa-ellipsis-h text-grey-dark" @click="isVisible = ! isVisible"></i>
        </div>
        <div class="dropdown-button-items items-center" :class="directionClass" v-if="isVisible">
            <div class="dropdown-button-caret">
                <div class="dropdown-button-caret-outer"></div>
                <div class="dropdown-button-caret-inner"></div>
            </div>
            <slot></slot>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
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

        computed: {
            directionClass: function () {
                return 'direction-' + this.direction;
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