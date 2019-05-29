<template>
    <div class="relative" style="width: 24rem">
        <input type="search" placeholder="Suche" class="form-control form-input form-input-bordered w-full shadow" @keyup="changed()" v-model="value">
        <ul class="absolute bg-white shadow rounded-lg right-0 z-50 border border-gray-600" v-show="suggestions.length > 0">
            <li v-for="item in suggestions" class="whitespace-no-wrap p-2 text-xs rounded-lg hover:bg-gray-400 cursor-pointer" @click="selected(item)">{{ item.name }}</li>
        </ul>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        data () {
            return {
                value: '',
                suggestions : []
            }
        },
        methods: {
            selected (selected) {
                window.location.href = selected.link;
            },
            changed: function() {
                var that = this
                this.suggestions = []
                if (this.value.length > 3) {
                    axios.post(route('global_search'), {
                        query: this.value
                    }).then(function(response) {
                        response.data.forEach(function(a) {
                            that.suggestions.push(a)
                        })
                    })
                }
            }
        }
    }
</script>