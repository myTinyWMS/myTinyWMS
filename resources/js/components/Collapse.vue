<template>
    <div class="card">
        <div class="card-header flex text-grey-dark">
            <h4 class="flex-1">{{ title }}</h4>
            <z icon="cheveron-down" class="h-6 w-6 fill-current text-grey-dark cursor-pointer" v-show="open" @click="open = false"></z>
            <z icon="cheveron-right" class="h-6 w-6 fill-current text-grey-dark cursor-pointer" v-show="!open" @click="open = true"></z>
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