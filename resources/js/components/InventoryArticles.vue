<template>
    <div v-if="items">
        <div class="card mb-4" v-for="(inventoryItems, category, index) in items">
            <div class="card-header flex cursor-pointer" @click="(open == index) ? open = null : open = index">
                <template v-if="inventory.inventoryIsFinished">
                    <div class="flex-1">{{ category }} - {{ countOpenArticles(inventoryItems) }} offene Artikel</div>
                </template>
                <template v-else>
                    <div class="flex-1">{{ category }}</div>
                </template>

                <div>
                    <span class="down-Arrow" v-show="open != index">&#9660;</span>
                    <span class="up-Arrow" v-show="open == index">&#9650;</span>
                </div>
            </div>
            <div class="card-content" v-show="open == index">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Artikel</th>
                            <th width="25%">Notizen</th>
                            <th class="w-16 text-nowrap">Bestand alt</th>
                            <th class="w-64 text-nowrap text-center">Bestand aktuell</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, itemkey, articleindex) in inventoryItems">
                            <td>{{ item.article.article_number }}</td>
                            <td>
                                <a v-bind:href="route('article.show', [item.article])">{{ item.article.name }}</a>
                                <small>{{ item.article.supplier_name }}</small>
                            </td>
                            <td>{{ item.article.notes }}</td>

                            <template v-if="inventory.inventoryIsFinished || item.processed_at">
                                <td class="text-center">{{ item.old_quantity }}</td>
                                <td class="text-center" :class="{ danger: (item.old_quantity != item.new_quantity) }">
                                    {{ item.new_quantity }}

                                    <span v-if="item.processed_at" :title="(item.processor ? item.processor.name : 'unknown') + ' - ' + formatDate(item.processed_at, 'DD.MM.YYYY HH:mm')">
                                        <z icon="information-outline" class="fill-current w-4 h-4 ml-4"></z>
                                    </span>
                                </td>
                            </template>
                            <template v-else>
                                <td class="text-center p-t-15">
                                    {{ item.article.quantity }}
                                    <br>
                                    <small>{{ item.article.unit ? item.article.unit.name : '' }}</small>

                                    <div class="m-t-sm" v-if="item.article.outsourcing_quantity !== 0">
                                        <b class="text-danger">Außenlager:</b> {{ item.article.outsourcing_quantity }}
                                    </div>

                                    <div class="m-t-sm" v-if="item.article.replacement_delivery_quantity !== 0">
                                        <b class="text-danger">Ersatzlieferung:</b> {{ item.article.replacement_delivery_quantity }}
                                    </div>
                                </td>
                                <td class="text-center text-nowrap">
                                    <div class="flex" v-if="finishedArticles.indexOf(item.article.id) == -1">
                                        <input type="text" class="form-input w-16 py-0" name="quantity" v-model="item.article.new_quantity">

                                        <button type="button" @click="save(item.article, 'new_quantity')" class="btn btn-warning ml-2" title="Bestand ändern">
                                            <z icon="save-disk" class="fill-current w-4 h-4"></z>
                                        </button>

                                        <div class="mx-2 text-xl">|</div>

                                        <button type="button" @click="save(item.article, 'quantity')" class="btn btn-success" title="Bestand stimmmt">
                                            <z icon="checkmark" class="fill-current w-4 h-4"></z>
                                        </button>
                                    </div>

                                    <span class="text-green-500">
                                        <z icon="checkmark" class="fill-current w-8 h-8 m-auto" v-if="finishedArticles.indexOf(item.article.id) > -1"></z>
                                    </span>
                                </td>
                            </template>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        props: ['items', 'inventory', 'inventoryIsFinished'],

        data() {
            return {
                finishedArticles: [],
                quantity: [],
                open: 0,
                csrf: ""
            };
        },

        methods: {
            formatDate(date, format) {
                return moment(date).format(format);
            },

            save(article, property) {
                let that = this;

                $.post(route('inventory.processed', [that.inventory.id, article.id]), {quantity: article[property]}).done(function (data) {
                    if (data) {
                        that.finishedArticles.push(article.id);
                    }
                });
            },

            countOpenArticles(items) {
                let that = this;
                return _.filter(items, function (item) {
                    return (that.finishedArticles.indexOf(item.article.id) == -1 && item.processed_by == null);
                }).length;
            }
        },

        mounted() {
            this.csrf = document.head.querySelector('meta[name="csrf-token"]').content;
        },
    }
</script>