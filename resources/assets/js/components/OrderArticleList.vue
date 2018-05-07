<template>
    <div>
        <div class="panel panel-primary" v-for="(article, index) in articles" :key="index">
            <div class="panel-body row">
                <div class="col-lg-6">
                    <!--{{ Form::bsSelect('article[]', null, [],  'Artikel', ['class' => 'form-control article-select']) }}-->
                    <div class="form-group">
                        <label class="control-label">Name</label>
                        <div class="form-control-static">
                            {{ article.name }}
                            <button type="button" class="btn btn-primary btn-xs" v-bind:class="{ 'm-l-md': (article.id) }" data-toggle="modal" data-target="#articleSelectModal" @click.prevent="saveCurrentIndex(index)">ändern</button>
                        </div>
                    </div>

                    <div class="alert alert-warning" v-if="article.order_notes">
                        <i class="fa fa-exclamation-triangle"></i> {{ article.order_notes }}
                    </div>
                </div>
                <div class="col-lg-2 text-right">
                    <div class="form-group">
                        <label for="quantity[]" class="control-label">Menge</label>
                        <input class="form-control text-right quantity-select" required="required" name="quantity[]" id="quantity[]" type="text" v-model="article.quantity">
                    </div>
                </div>
                <div class="col-lg-2 text-right">
                    <div class="form-group">
                        <label for="price[]" class="control-label">Preis je Einheit</label>
                        <input class="form-control text-right price-select" required="required" name="price[]" id="price[]" type="text" v-model="article.price">
                    </div>
                </div>
                <div class="col-lg-2 text-right">
                    <div class="form-group">
                        <label for="expected_delivery[]" class="control-label">Liefertermin</label>
                        <input class="form-control datepicker delivery-input" name="expected_delivery[]" id="expected_delivery[]" type="text" v-model="article.expected_delivery">
                    </div>
                </div>
                <a href="#" class="btn btn-xs btn-default btn-circle delete-btn" @click.prevent="removeArticle(index)"><i class="glyphicon glyphicon-remove"></i></a>
            </div>
        </div>

        <button class="btn btn-primary btn-sm" @click.prevent="addArticle()">Artikel hinzufügen</button>
    </div>
</template>

<script>
    export default {
        props: ['allArticles', 'existingArticles'],

        data() {
            return {
                articles: [],
                currentIndex: null
            }
        },

        created() {
            this.addArticle();
        },

        methods: {
            addArticle() {
                this.articles.push({
                    id: null,
                    name: null,
                    order_notes: '',
                    quantity: '',
                    price: '',
                    expected_delivery: ''
                })
            },

            removeArticle(index) {
                this.articles.splice(index, 1);
            },

            saveCurrentIndex(currentIndex) {
                this.currentIndex = currentIndex;
            },

            formatPrice(value) {
                return value.toString().replace('.', ',');
            },

            selectArticle(id) {
                console.log('got', id);
                let article = _.find(this.allArticles, _.matchesProperty('id', id));
                console.log(article);
                this.articles[this.currentIndex].id = article.id;
                this.articles[this.currentIndex].name = article.name;
                this.articles[this.currentIndex].order_notes = article.order_notes;
                this.articles[this.currentIndex].quantity = article.order_quantity;
                this.articles[this.currentIndex].price = this.formatPrice(article.price);
                this.articles[this.currentIndex].expected_delivery = (moment(article.delivery_date).isValid() ? moment(article.delivery_date).format('DD.MM.YYYY') : '');
                console.log(id, this.currentIndex);
            }
        }
    }
</script>