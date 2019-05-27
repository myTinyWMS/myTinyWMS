<template>
    <div class="card">
        <div class="card-header flex text-grey-dark">
            <h4 class="flex-1">{{ title }}</h4>
            <a href="javascript:void(0)" v-show="open" @click="open = false"><z icon="cheveron-down" class="h-6 w-6 fill-current text-grey-dark cursor-pointer"></z></a>
            <a href="javascript:void(0)" v-show="!open" @click="open = true"><z icon="cheveron-right" class="h-6 w-6 fill-current text-grey-dark cursor-pointer"></z></a>
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