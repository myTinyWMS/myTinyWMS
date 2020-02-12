<template>
    <div>
        <button type="button" class="btn btn-secondary border-green-600 text-green-600" @click="click()">
            <z icon="checkmark" class="fill-current w-3 h-3 inline-block"></z> {{ $t('alle Rechnungen erhalten') }}
        </button>

        <change-article-price-modal :order="order" :name="'changeArticlePriceAllModal'"></change-article-price-modal>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        props: ['order', 'articleHasNewPrice'],

        methods: {
            click() {
                if (this.articleHasNewPrice) {
                    this.$modal.show('changeArticlePriceAllModal');
                } else {
                    this.setStatus();
                }
            },
            setStatus() {
                axios.post(route('order.all_items_invoice_received', {order: this.order}), {
                    change_article_price: 0
                }).then(function () {
                    location.reload();
                });
            }
        }
    }
</script>