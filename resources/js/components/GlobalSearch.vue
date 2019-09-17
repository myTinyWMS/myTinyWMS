<template>
    <div class="relative" style="width: 24rem">
        <input type="search" placeholder="Suche" class="form-control form-input form-input-bordered w-full shadow" @keyup="changed" v-model="value">
        <div class="absolute bg-white shadow rounded left-0 z-50 border border-gray-400 mt-2 p-2 w-full" v-show="suggestions.length > 0">
            <div v-for="(group, groupindex) in suggestions">
                <div class="text-xs pl-2 border-b text-black">{{ group.name }}</div>
                <div v-for="(item, index) in group.items" class="whitespace-no-wrap p-2 text-sm hover:bg-gray-200 cursor-pointer bg-white" :title="group.name + ' ' + item.title" :class="{ 'bg-gray-200': selectedItem == (1 + index + groupindex), 'rounded-t': index == 0, 'rounded-b': index == (suggestions.length - 1) }" @click="selected(item)">{{ item.name }}</div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        data () {
            return {
                value: '',
                suggestions : [],
                selectedItem: 0
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
                        that.selectedItem = 1;
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
                        let list = [];
                        $.each(that.suggestions, function (key, items) {
                            list.push(items.name);
                            $.each(items.items, function (index, item) {
                                list.push(item);
                            });
                        });

                        that.selected(list[that.selectedItem]);
                    }
                }
            });
        }
    }
</script>