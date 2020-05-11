<template>
    <form method="post" v-bind:action="route('article-group.change_quantity', [articleGroup.id])" @submit="submit">
        <h4 class="modal-title">{{ $t('Bestand ändern') }}</h4>

        <div class="row">
            <div class="flex">
                <div class="form-group mr-6">
                    <label class="form-label">{{ $t('Veränderung') }}</label>
                    <div class="flex">
                        <select v-model="changelogChangeType" name="changelogChangeType" id="changelogChangeType">
                            <option value="add">{{ $t('Plus') }}</option>
                            <option value="sub">{{ $t('Minus') }}</option>
                        </select>
                    </div>
                </div>

                <div class="form-group mr-6">
                    <label for="changelogType" class="form-label">{{ $t('Typ der Änderung') }}</label>
                    <input type="hidden" name="changelogType" v-model="changelogType.value" />
                    <select id="changelogType" class="form-control" required v-model="changelogType">
                        <option value="" selected disabled></option>
                        <option v-for="item in changeTypes" v-bind:value="item" v-if="(item.ifOnly == '' || changelogChangeType == item.ifOnly)">{{ item.text }}</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="set_quantity" class="form-label">{{ changelogChangeType == 'sub' ? $t('Wieviele Sets sollen ausgebucht werden?') : $t('Wieviele Sets sollen eingebucht werden?') }}</label>
                    <input type="text" name="set_quantity" id="set_quantity" class="form-input w-24" v-model="set_quantity" v-on:change="updateQuantities" />
                </div>
            </div>
        </div>

        <div class="rounded border border-blue-700 p-4 mb-4" v-for="(item,key) in articleGroup.items">
            <div class="row">
                <div class="w-7/12">
                    <div class="form-group">
                        <label class="form-label">{{ $t('Artikel') }} {{ key+1 }}</label>
                        <div class="form-control-static">
                            {{ item.article.name }}
                            <div class="text-xs my-2"># {{ item.article.internal_article_number }}</div>
                        </div>
                    </div>
                </div>
                <div class="w-5/12">
                    <div class="row">
                        <div class="w-1/3">
                            <div class="form-group">
                                <label class="form-label">{{ $t('Aktueller Bestand') }}</label>
                                <div class="form-control-static">
                                    {{ item.article.quantity }}
                                </div>
                            </div>
                        </div>
                        <div class="w-1/3">
                            <div class="form-group">
                                <label class="form-label">{{ $t('Gruppen Menge') }}</label>
                                <div class="form-control-static">
                                    {{ item.quantity }}
                                </div>
                            </div>
                        </div>
                        <div class="w-1/3">
                            <div class="form-group">
                                <label class="form-label">{{ $t('Menge') }}</label>
                                <div class="form-control-static">
                                    <input type="text" class="form-input w-20" v-model="quantities[item.id]" :id="'quantity_' + key" :name="'quantity[' + item.id + ']'">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="changelogNote" class="form-label">{{ $t('Bemerkung') }}</label>
            <textarea class="form-textarea" rows="3" id="changelogNote" name="changelogNote"></textarea>
        </div>

        <div class="modal-footer">
            <input type="hidden" v-bind:value="csrf" name="_token" />
            <button type="button" class="btn btn-default" @click="$modal.hide('change-quantity')">{{ $t('Abbrechen') }}</button>
            <button type="submit" class="btn btn-primary" id="submitChangeQuantity">{{ $t('Speichern') }}</button>
        </div>
    </form>
</template>

<script>
    export default {
        props: ['articleGroup'],

        data() {
            return {
                set_quantity: 1,
                quantities: [],
                changelogChangeType: 'sub',
                changelogType: {value: ''},
                change: '',
                csrf: "",
                changeTypes: [
                    {value: 1, text: this.$t('Wareneingang'), ifOnly: 'add'},
                    {value: 2, text: this.$t('Warenausgang'), ifOnly: 'sub'},
                    {value: 7, text: this.$t('Inventur'), ifOnly: ''},
                    {value: 8, text: this.$t('Ersatzlieferung'), ifOnly: ''},
                    {value: 9, text: this.$t('Ein-/Auslagerung Aussenlager'), ifOnly: ''},
                    {value: 10, text: this.$t('Verkauf an Fremdfirmen'), ifOnly: 'sub'},
                    {value: 11, text: this.$t('Umbuchung'), ifOnly: ''},
                ]
            }
        },

        watch: {
            set_quantity: function () {
                this.updateQuantities();
            }
        },

        methods: {
            updateQuantities() {
                let that = this;
                _.forEach(that.articleGroup.items, function (item) {
                    that.quantities[item.id] = item.quantity * that.set_quantity;
                });
            },
            submit(e) {
                let that = this;
                if (this.changelogChangeType == 'sub') {
                    let breakSubmit = false;
                    _.forEach(that.articleGroup.items, function (item) {
                        if (that.quantities[item.id] > item.article.quantity) {
                            alert(that.$t('Es ist nicht möglich mehr auszubuchen als Bestand vorhanden ist!'));
                            breakSubmit = true;
                            return false;
                        }
                    });

                    if (breakSubmit) {
                        e.preventDefault();
                        return false;
                    }
                }

                let message = this.$t('Du willst den Bestand um ändern als "') + this.changelogType.text + '". ' + this.$t('SICHER?');

                if (!confirm(message)) {
                    e.preventDefault();
                    return false;
                }
            }
        },

        mounted() {
            this.csrf = document.head.querySelector('meta[name="csrf-token"]').content;
            this.updateQuantities();
        }
    }
</script>