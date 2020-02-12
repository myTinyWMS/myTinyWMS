<template>
    <modal :name="name" height="auto" classes="modal">
        <h4 class="modal-title text-red-500 font-bold text-xl">{{ $t('Preisänderung!') }}</h4>

        <div class="row">
            <div class="w-full">
                <div class="text-base text-gray-800 tracking-tight" v-if="order !== false">
                    {{ $t('Der Preis mind. eines Artikels in dieser Bestellung weicht vom aktuellen Artikelpreis ab.') }}<br>
                    {{ $t('Soll der Artikelpreis angepasst werden?') }}
                </div>
                <div class="text-base text-gray-800 tracking-tight" v-if="order === false">
                    {{ $t('Der Preis dieses Artikels in dieser Bestellung weicht vom aktuellen Artikelpreis ab.') }}<br>
                    {{ $t('Soll der Artikelpreis angepasst werden?') }}
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default" @click="submit(0)">{{ $t('Nein Artikelpreis nicht ändern') }}</button>
            <button type="button" class="btn btn-primary" @click="submit(1)">{{ $t('Ja Artikelpreis anpassen') }}</button>
        </div>
    </modal>

</template>

<script>
    import axios from 'axios';

    export default {
        props: {name, status, orderitem: {default: false}, order: {default: false}},

        methods: {
            submit(change_article_price) {
                if (parseInt(this.order) > 0) {
                    axios.post(route('order.all_items_invoice_received', {order: this.order}), {
                        change_article_price: change_article_price
                    }).then(function () {
                        location.reload();
                    });
                } else {
                    axios.post(route('order.item_invoice_received', {orderitem: this.orderitem}), {
                        invoice_status: this.status,
                        change_article_price: change_article_price
                    }).then(function () {
                        location.reload();
                    });
                }
            }
        }
    }
</script>