<template>
    <form method="post" v-bind:action="route('article.change_quantity', [article.id])" @submit="submit">
        <h4 class="modal-title">Bestand ändern</h4>
        <div class="row">
            <div class="w-1/2">
                <div class="form-group">
                    <label for="changelogCurrentQuantity" class="form-label">aktueller Bestand</label>
                    <div class="form-control-static">
                        <span id="changelogCurrentQuantity">{{ article.quantity }}</span>
                        {{ unit }}
                    </div>
                </div>
            </div>
            <div class="w-1/3 col-lg-offset-2">
                <div class="form-group">
                    <label class="form-label">Entnahmemenge</label>
                    <div class="form-control-static">
                        <span>{{ article.issue_quantity }}</span>
                        {{ unit }}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="w-1/2">
                <div class="form-group">
                    <label class="form-label">Veränderung</label>

                    <div class="flex">
                        <select v-model="changelogChangeType" name="changelogChangeType">
                            <option value="add">Plus</option>
                            <option value="sub">Minus</option>
                        </select>
                        <input class="form-input w-24 ml-2" type="text" v-model="change" value="" name="changelogChange" placeholder="Menge" required>
                    </div>
                </div>
            </div>
            <div class="w-1/2">
                <div class="form-group">
                    <label for="changelogType" class="form-label">Typ der Änderung</label>
                    <input type="hidden" name="changelogType" v-model="changelogType.value" />
                    <select id="changelogType" class="form-control" required v-model="changelogType">
                        <option value="" selected disabled></option>
                        <option v-for="item in changeTypes" v-bind:value="item" v-if="(item.ifOnly == '' || changelogChangeType == item.ifOnly)">{{ item.text }}</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="changelogNote" class="form-label">Bemerkung</label>
            <textarea class="form-textarea" rows="3" id="changelogNote" name="changelogNote"></textarea>
        </div>

        <div class="modal-footer">
            <input type="hidden" v-bind:value="csrf" name="_token" />
            <button type="button" class="btn btn-default" @click="$modal.hide('change-quantity')">Abbrechen</button>
            <button type="submit" class="btn btn-primary">Speichern</button>
        </div>
    </form>
</template>

<script>
    export default {
        props: ['article', 'unit'],

        data() {
            return {
                changelogChangeType: 'add',
                changelogType: {value: ''},
                change: '',
                csrf: "",
                changeTypes: [
                    {value: 1, text: 'Wareneingang', ifOnly: 'add'},
                    {value: 2, text: 'Warenausgang', ifOnly: 'sub'},
                    {value: 7, text: 'Inventur', ifOnly: ''},
                    {value: 8, text: 'Ersatzlieferung', ifOnly: ''},
                    {value: 9, text: 'Ein-/Auslagerung Aussenlager', ifOnly: ''},
                    {value: 10, text: 'Verkauf an Fremdfirmen', ifOnly: 'sub'},
                    {value: 11, text: 'Umbuchung', ifOnly: ''},
                ]
            }
        },

        methods: {
            submit(e) {
                if (this.changelogChangeType == 'sub' && this.change > this.article.quantity) {
                    alert('Es ist nicht möglich mehr auszubuchen als Bestand vorhanden ist!');
                    e.preventDefault();
                    return false;
                }

                var message = 'Du willst den Bestand um ';
                message += (this.changelogChangeType === 'sub') ? 'MINUS ' : 'PLUS ';
                message += this.change + ' ändern - als ';
                message += '"' + this.changelogType.text + '". SICHER?';

                if (!confirm(message)) {
                    e.preventDefault();
                }
            }
        },

        mounted() {
            this.csrf = document.head.querySelector('meta[name="csrf-token"]').content;
        }
    }
</script>