
<template>
    <modal name="invoiceCheckModal" height="auto" classes="modal" :clickToClose="false">
        <h4 class="modal-title font-bold text-xl">{{ $t('Rechnungsprüfung - Mail an Einkaufsteam') }}</h4>

        <div class="row">
            <div class="w-full">
                <div class="form-group">
                    <label for="invoice_check_note" class="form-label">{{ $t('Bemerkungen zur Rechnung') }}</label>
                    <textarea id="invoice_check_note" v-model="mail_note" class="form-textarea" rows="3"></textarea>
                </div>
                <vue-dropzone ref="myVueDropzone" id="dropzone" :options="dropzoneOptions" :useCustomSlot="true" v-if="demo == 1" v-on:vdropzone-complete="uploadComplete">
                    <div class="dropzone-custom-content">
                        <h3 class="dropzone-custom-title">{{ $t('Dateien hier ablegen') }}</h3>
                    </div>
                </vue-dropzone>
            </div>
        </div>

        <div class="row alert alert-danger mt-4" v-show="invoiceNotificationUsersCount == 0">
            {{ $t('Achtung, kein User hat die Rechnungsbenachrichtigungen aktiv!') }}
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default" @click="submit(false)" id="dont-send-invoice-check-mail">{{ $t('Ohne Mail weiter') }}</button>
            <button type="button" class="btn btn-primary" @click="submit(true)" id="send-invoice-check-mail" v-show="invoiceNotificationUsersCount > 0">{{ $t('Mail verschicken') }}</button>
        </div>
    </modal>

</template>

<script>
    import axios from 'axios';

    export default {
        props: ['status', 'orderitem', 'invoiceNotificationUsersCount', 'demo'],

        data() {
            return {
                mail_note: "",
                mail_attachments: [],
                dropzoneOptions: {
                    url: route('order.invoice_check_upload', {order: this.orderitem.order_id}),
                    clickable: false    // doesn't work with modal, closes modal on click -> https://github.com/rowanwins/vue-dropzone/issues/457
                }
            };
        },

        methods: {
            uploadComplete(response) {
                let file = {
                    'tempFile': JSON.parse(response.xhr.response),
                    'orgName': response.name,
                    'type': response.type
                };
                this.mail_attachments.push(file);
            },
            submit(sendMail) {
                axios.post(route('order.item_invoice_received', {orderitem: this.orderitem.id}), {
                    invoice_status: this.status,
                    change_article_price: 0,
                    mail_note: (sendMail) ? this.mail_note : '',
                    mail_attachments: (sendMail) ? this.mail_attachments : '',
                }).then(function () {
                    location.reload();
                });
            }
        }
    }
</script>