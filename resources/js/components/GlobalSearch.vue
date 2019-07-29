<template>
    <div class="relative" style="width: 24rem">
        <input type="search" placeholder="Suche" class="form-control form-input form-input-bordered w-full shadow" @keyup="changed" v-model="value">
        <ul class="absolute bg-white shadow rounded-lg right-0 z-50 border border-gray-600" v-show="suggestions.length > 0">
            <li v-for="(item, index) in suggestions" class="whitespace-no-wrap p-2 text-xs hover:bg-gray-400 cursor-pointer" :class="{ 'bg-gray-400': selectedItem == index, 'rounded-t-lg': index == 0, 'rounded-b-lg': index == (suggestions.length - 1) }" @click="selected(item)">{{ item.name }}</li>
        </ul>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        data () {
            return {
                value: '',
                suggestions : [],
                selectedItem: -1
            }
        },
        methods: {
            selected (selected) {
                window.location.href = selected.link;
            },
            changed: function(event) {
                let keycode = event.keyCode;
                let valid =
                    (keycode > 47 && keycode < 58) || // number keys
                    keycode == 32 || // spacebar
                    (keycode > 64 && keycode < 91) || // letter keys
                    (keycode > 95 && keycode < 112) || // numpad keys
                    (keycode > 185 && keycode < 193) || // ;=,-./` (in order)
                    (keycode > 218 && keycode < 223);   // [\]' (in order)

                if (!valid) {
                    return false;
                }

                var that = this;
                this.suggestions = [];
                if (this.value.length > 3) {
                    axios.post(route('global_search'), {
                        query: this.value
                    }).then(function(response) {
                        that.suggestions = [];
                        response.data.forEach(function(a) {
                            that.suggestions.push(a)
                        })
                    })
                }
            }
        },
        mounted: function() {
            let that = this;
            window.addEventListener('keyup', function(e) {
                if (that.suggestions.length > 0){
                    if (e.keyCode == 40) {
                        that.selectedItem++;
                    } else if (e.keyCode == 38) {
                        that.selectedItem--;
                        if (that.selectedItem < -1) {
                            that.selectedItem = -1;
                        }
                    } else if (e.keyCode == 13) {
                        that.selected(that.suggestions[that.selectedItem]);
                    }
                }
            });
        }
    }
</script>