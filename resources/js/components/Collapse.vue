<template>
    <div class="card">
        <div class="card-header flex">
            <h4 class="flex-1">{{ title }}</h4>
            <i class="fa fa-chevron-down text-grey-dark cursor-pointer" v-if="open" @click="open = false"></i>
            <i class="fa fa-chevron-right text-grey-dark cursor-pointer" v-if="!open" @click="open = true"></i>
        </div>
        <div class="card-content" v-show="open">
            <slot></slot>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['title'],

        data() {
            return {
                open: false
            }
        }
    }
</script>