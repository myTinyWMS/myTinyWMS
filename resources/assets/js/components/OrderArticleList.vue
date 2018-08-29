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
                            <button type="button" class="btn btn-primary btn-xs" v-if="supplier" v-bind:class="{ 'm-l-md': (article.id) }" data-toggle="modal" data-target="#articleSelectModal" @click.prevent="showArticleList(index)">Artikel auswählen</button>
                            <div class="text-danger" v-if="!supplier">Bitte zuerst einen Lieferanten auswählen!</div>
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
                        <label for="price[]" class="control-label">Preis netto je Einheit</label>
                        <input class="form-control text-right price-select" required="required" name="price[]" id="price[]" type="text" v-model="article.price">
                    </div>
                </div>
                <div class="col-lg-2 text-right">
                    <div class="form-group">
                        <label :for="'expected_delivery' + index" class="control-label">Liefertermin</label>
                        <input class="form-control datepicker delivery-input" name="expected_delivery[]" :id="'expected_delivery' + index" :data-index="index" type="text" v-model="article.expected_delivery">
                    </div>
                </div>
                <a href="#" class="btn btn-xs btn-default btn-circle delete-btn" @click.prevent="removeArticle(index)"><i class="glyphicon glyphicon-remove"></i></a>
            </div>
        </div>

        <button class="btn btn-primary btn-sm" @click.prevent="addArticle(true)">Artikel hinzufügen</button>
    </div>
</template>

<script>
    export default {
        props: ['allArticles', 'existingArticles', 'supplier', 'articles'],

        data() {
            return {
                currentIndex: null
            }
        },

        created() {
            if (!this.articles.length) {
                this.addArticle(false);
            }
        },

        mounted() {
            this.handleDatepicker();
        },

        updated() {
            this.handleDatepicker();
        },

        methods: {
            handleDatepicker() {
                var that = this;
                $(".delivery-input").datepicker({
                    format: 'dd.mm.yyyy',
                    language: 'de',
                    todayHighlight: true,
                    daysOfWeekDisabled: [0,6],
                    autoclose: true,
                    calendarWeeks: true
                }).on(
                    "changeDate", function (e) {
                        that.articles[$(this).attr('data-index')].expected_delivery = moment(e.date).format('DD.MM.YYYY');
                    }
                );
            },

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
                    $('#articleSelectModal').modal('show');
                    this.showArticleList(this.articles.length - 1);
                }
            },

            removeArticle(index) {
                this.articles.splice(index, 1);
            },

            showArticleList(currentIndex) {
                this.currentIndex = currentIndex;

                let currentSupplierId = this.supplier;
                let allArticles = _.map(this.allArticles, function (o) {
                    if (o.supplier_id == currentSupplierId) return o;
                });
                allArticles = _.without(allArticles, undefined);
                let notExistingIds = _.difference(_.map(allArticles, 'id'), _.map(this.articles, 'id'));
                window.LaravelDataTables.dataTableBuilder.columns(17).search(notExistingIds).draw();
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
                this.articles[this.currentIndex].expected_delivery = (moment(article.delivery_date).isValid() ? moment(article.delivery_date).format('DD.MM.YYYY') : '');
            }
        }
    }
</script>