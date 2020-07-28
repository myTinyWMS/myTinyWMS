<template>
    <div>
        <div class="rounded border border-blue-700 p-4 mb-4 relative order-article" v-for="(article, index) in articles" :key="index">
            <div class="row flex">
                <div class="flex-1">
                    <div class="form-group">
                        <label class="form-label">
                            {{ $t('Artikel') }}

                            <dot-menu direction="right" class="article-menu">
                                <a v-bind:class="{ 'm-l-md': (article.id), 'change-article': true }" @click.prevent="showArticleList(index)">{{ $t('Artikel ändern') }}</a>
                                <a @click.prevent="removeArticle(index)" class="delete-article">{{ $t('Artikel löschen') }}</a>
                            </dot-menu>
                        </label>
                        <div class="form-control-static article-name">
                            {{ article.name }}
                            <div class="text-xs my-2"># {{ article.internal_article_number }}</div>
                        </div>
                    </div>
                </div>
                <div class="w-48 mr-4 text-right">
                    <div class="form-group">
                        <label :for="'quantity_' + index" class="form-label">{{ $t('Menge') }}</label>
                        <input class="form-input text-right quantity-select" required="required" name="quantity[]" :id="'quantity_' + index" type="text" v-model="article.quantity">
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="article_data" v-model="articleData">
        <button class="btn btn-secondary btn-sm" id="add-article" @click.prevent="addArticle(true)">{{ $t('Artikel hinzufügen') }}</button>
    </div>
</template>

<script>
    import { serverBus } from '../app';

    export default {
        props: ['allArticles', 'existingArticles'],

        data() {
            return {
                articles: [],
                currentIndex: null
            }
        },

        computed: {
            articleData: function() {
                return JSON.stringify(this.articles);
            }
        },

        created() {
            if (this.existingArticles.length) {
                this.articles = this.existingArticles;
            }
        },

        methods: {
            addArticle(showArticleList) {
                this.articles.push({
                    id: null,
                    article_group_item_id: null,
                    name: null,
                    internal_article_number: null,
                    quantity: '',
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
                this.$modal.show('selectOrderArticleModal');
            },

            formatPrice(value) {
                return value.toString().replace('.', ',');
            },

            selectArticle(id) {
                let article = _.find(this.allArticles, _.matchesProperty('id', id));
                console.log(id, this.allArticles, article);
                this.articles[this.currentIndex].id = article.id;
                this.articles[this.currentIndex].internal_article_number = article.internal_article_number;
                this.articles[this.currentIndex].article_group_item_id = null;
                this.articles[this.currentIndex].name = article.name;
                this.articles[this.currentIndex].quantity = article.usage_quantity;

                this.$modal.hide('selectOrderArticleModal');
            },
        }
    }
</script>