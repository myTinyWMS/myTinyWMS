<template>
    <dot-menu class="ml-2 normal-case">
        <a href="javascript:void(0)" @click="checkNewArticlePrice(1)">erhalten</a>
        <a href="javascript:void(0)" @click="sendInvoiceCheckMail(2)">in Prüfung</a>
        <a href="javascript:void(0)" @click="setStatus(0)">nicht erhalten</a>

        <change-article-price-modal :status="1" :orderitem="item.id" :name="'changeArticlePriceSingleModal'"></change-article-price-modal>
        <invoice-check-modal :status="2" :orderitem="item" :invoice-notification-users-count="invoiceNotificationUsersCount"></invoice-check-modal>
    </dot-menu>
</template>

<script>
    import axios from 'axios';

    export default {
        props: ['item', 'articleHasNewPrice', 'invoiceNotificationUsersCount'],

        methods: {
            sendInvoiceCheckMail() {
                this.$modal.show('invoiceCheckModal');
            },
            checkNewArticlePrice(status) {
                if (this.articleHasNewPrice == 1) {
                    this.$modal.show('changeArticlePriceSingleModal');
                } else {
                    this.setStatus(status);
                }
            },
            setStatus(status) {
                axios.post(route('order.item_invoice_received', {orderitem: this.item.id}), {
                    invoice_status: status,
                    change_article_price: 0
                }).then(function () {
                    location.reload();
                });
            }
        }

        //
    }
</script>