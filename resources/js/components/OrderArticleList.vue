<template>
    <div>
        <div class="rounded border border-blue-700 p-4 mb-4 relative order-article" v-for="(article, index) in articles" :key="index">
            <div class="row flex">
                <div class="flex-1">
                    <div class="form-group">
                        <label class="form-label">
                            {{ $t('Artikel') }}

                            <dot-menu direction="right" class="article-menu">
                                <a v-if="supplier" v-bind:class="{ 'm-l-md': (article.id), 'change-article': true }" @click.prevent="showArticleList(index)">{{ $t('Artikel ändern') }}</a>
                                <a @click.prevent="removeArticle(index)" class="delete-article">{{ $t('Artikel löschen') }}</a>
                            </dot-menu>
                        </label>
                        <div class="form-control-static article-name">
                            {{ article.name }}
                        </div>
                    </div>

                    <div class="alert alert-warning" v-if="article.order_notes">
                        <i class="fa fa-exclamation-triangle"></i> {{ article.order_notes }}
                    </div>
                </div>
                <div class="w-48 mr-4 text-right">
                    <div class="form-group">
                        <label for="quantity[]" class="form-label">{{ $t('Menge') }}</label>
                        <input class="form-input text-right quantity-select" required="required" name="quantity[]" id="quantity[]" type="text" v-model="article.quantity">
                    </div>
                </div>
                <div class="w-48 mr-4 text-right">
                    <div class="form-group">
                        <label for="price[]" class="form-label">{{ $t('Preis netto je Einheit') }}</label>
                        <input class="form-input text-right price-select" required="required" name="price[]" id="price[]" type="text" v-model="article.price">
                    </div>
                </div>
                <div class="w-48 text-right">
                    <div class="form-group">
                        <label :for="'expected_delivery' + index" class="form-label">{{ $t('Liefertermin') }}</label>
                        <input type="hidden" name="expected_delivery[]" v-model="article.expected_delivery">
                        <date-picker v-model="article.expected_delivery" :id="'expected_delivery' + index" class="deliverydate-input"></date-picker>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-red-500" v-if="!supplier">{{ $t('Bitte zuerst einen Lieferanten auswählen!') }}</div>
        <button class="btn btn-secondary btn-sm" id="add-article" v-if="supplier" @click.prevent="addArticle(true)">{{ $t('Artikel hinzufügen') }}</button>
    </div>
</template>

<script>
    import { serverBus } from '../app';

    export default {
        props: ['allArticles', 'existingArticles', 'supplier', 'articles'],

        data() {
            return {
                currentIndex: null
            }
        },

        mounted() {
            if(this.existingArticles.length > 0) {
                this.filterArticleList();
            }
        },

        watch: {
            supplier: function () {
                this.filterArticleList();
            }
        },

        methods: {
            addArticle(showArticleList) {
                this.articles.push({
                    id: null,
                    order_item_id: null,
                    name: null,
                    order_notes: '',
                    quantity: '',
                    price: '',
                    expected_delivery: ''
                });

                if (showArticleList) {
                    this.showArticleList(this.articles.length - 1);
                }
            },

            removeArticle(index) {
                this.articles.splice(index, 1);
            },

            showArticleList(currentIndex) {
                this.currentIndex = currentIndex;
                this.filterArticleList();
                this.$modal.show('selectOrderArticleModal');
            },

            filterArticleList() {
                let currentSupplierId = this.supplier;
                let allArticles = _.filter(this.allArticles, function (o) {
                    return (o.supplier_id == currentSupplierId);
                });
                allArticles = _.without(allArticles, undefined);
                let notExistingIds = _.difference(_.map(allArticles, 'id'), _.map(this.articles, 'id'));
                serverBus.$emit('filterOrderArticleList', notExistingIds);
            },

            formatPrice(value) {
                return value.toString().replace('.', ',');
            },

            selectArticle(id) {
                let article = _.find(this.allArticles, _.matchesProperty('id', id));
                this.articles[this.currentIndex].id = article.id;
                this.articles[this.currentIndex].order_item_id = null;
                this.articles[this.currentIndex].name = article.name;
                this.articles[this.currentIndex].order_notes = article.order_notes;
                this.articles[this.currentIndex].quantity = article.order_quantity;
                this.articles[this.currentIndex].price = this.formatPrice(article.price);
                this.articles[this.currentIndex].expected_delivery = (moment(article.delivery_date).isValid() ? moment(article.delivery_date).format('YYYY-MM-DD') : '');

                this.$modal.hide('selectOrderArticleModal');
            }
        }
    }
</script>