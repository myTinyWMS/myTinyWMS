<template>
    <div class="relative" style="width: 24rem">
        <input type="search" placeholder="Suche" class="form-control form-input w-full shadow bg-gray-800 border-gray-800 text-white" v-model="value">
        <div class="absolute bg-white shadow rounded left-0 z-50 border border-gray-400 mt-2 p-2 w-full" v-show="suggestions.length > 0" id="global-search-results">
            <div v-for="(group, groupindex) in suggestions">
                <div class="text-xs pl-2 border-b text-black">{{ group.name }}</div>
                <div v-for="(item, index) in group.items" class="whitespace-no-wrap p-2 text-sm hover:bg-gray-200 cursor-pointer bg-white" :title="group.name + ' ' + item.title" :class="{ 'bg-gray-200': selectedItem == (1 + index + groupindex), 'rounded-t': index == 0, 'rounded-b': index == (suggestions.length - 1) }" @click="selected(item)">
                    <div class="inline-block rounded bg-gray-600 w-2 h-2" v-if="item.status == 0" title="deaktiviert"></div>
                    <div class="inline-block rounded bg-green-500 w-2 h-2" v-if="item.status == 1" title="aktiv"></div>
                    <div class="inline-block rounded bg-orange-500 w-2 h-2" v-if="item.status == 2" title="Bestellstop"></div>
                    {{ item.name }}
                </div>
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
        watch: {
            value: function(){
                this.processSearch();
            },
        },
        methods: {
            selected (selected) {
                window.location.href = selected.link;
            },
            processSearch: function() {
                let that = this;

                that.suggestions = [];
                if (that.value.length > 3) {
                    axios.post(route('global_search'), {
                        query: that.value
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

            window.addEventListener('click', function (e) {
                if (e.target.id != 'global-search-results' && $(e.target).parents('#global-search-results').length == 0) {
                    that.suggestions = [];
                    that.selectedItem = 1;
                }
            });

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