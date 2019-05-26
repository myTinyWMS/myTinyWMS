<template>
    <!--<div class="bg-white relative">
        <div style="position: fixed; inset: 0px; z-index: 99998; background: black none repeat scroll 0% 0%; opacity: 0.2;" @click="isVisible = ! isVisible" v-if="isVisible"></div>
        <button class="btn btn-default" type="button" @click="isVisible = ! isVisible">
            <div class="flex items-baseline">
                <span class="text-grey-darkest hidden md:inline">{{ caption }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 961.243 599.998" class="w-2 h-2 fill-grey-darker md:ml-2"><path d="M239.998 239.999L0 0h961.243L721.246 240c-131.999 132-240.28 240-240.624 239.999-.345-.001-108.625-108.001-240.624-240z"></path></svg>
            </div>
        </button>
        <div v-if="isVisible" style="position: absolute; z-index: 99999; top: 27px; left: 0px; will-change: transform;" x-placement="bottom-start">
            <div class="mt-2 px-4 py-6 w-screen shadow-lg bg-white rounded dropdown-items" style="max-width: 300px;" v-html="content"></div>
        </div>
    </div>-->
    <div class="dropdown-button group" ref="dropdownMenu">
        <div class="dropdown-button-header btn btn-white flex text-base py-4 h-10 rounded-tr-none rounded-br-none border-r" @click="isVisible = ! isVisible">
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