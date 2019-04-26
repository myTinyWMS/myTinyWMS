<template>
    <div>
        <table class="table table-condensed table-bordered">
            <thead>
                <tr>
                    <th>Typ</th>
                    <th class="text-center">Änderung</th>
                    <th class="text-center">Bestand</th>
                    <th class="text-center">Einheit</th>
                    <th>Zeitpunkt</th>
                    <th>Kommentar</th>
                    <th>Benutzer</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in items" v-bind:class="{ 'border-thick': (item.id === highlightedRowId) }">
                    <template v-if="item.type === CHANGELOG_TYPE_INCOMING">
                        <td class="bg-success text-center">WE</td>
                        <td class="text-success text-center">+{{ item.change }}</td>
                        <td class="text-center">{{ item.new_quantity }}</td>
                    </template>

                    <template v-if="item.type === CHANGELOG_TYPE_OUTGOING">
                        <td class="bg-danger text-center">WA</td>
                        <td class="text-danger text-center">{{ item.change }}</td>
                        <td class="text-center">{{ item.new_quantity }}</td>
                    </template>

                    <template v-if="item.type === CHANGELOG_TYPE_CORRECTION">
                        <td class="bg-info text-center">
                            <span v-on:mouseover="highlightedRowId = item.related.id" v-on:mouseout="highlightedRowId = null" data-toggle="tooltip" data-placement="left" v-bind:title="getCorrectionTooltip(item)">KOR</span>
                        </td>
                        <td class="text-info text-center">{{ item.change >= 0 ? '+'+item.change : item.change }}</td>
                        <td class="text-center">{{ item.new_quantity }}</td>
                    </template>

                    <template v-if="item.type === CHANGELOG_TYPE_COMMENT">
                        <td class="bg-default text-center">KOM</td>
                        <td class="text-info text-center"></td>
                        <td class="text-center"></td>
                    </template>

                    <template v-if="item.type === CHANGELOG_TYPE_INVENTORY">
                        <td class="bg-primary text-center" title="Inventur">INV</td>
                        <td class="text-center" v-bind:class="{ 'text-success': (item.change >= 0), 'text-danger': (item.change < 0) }">{{ item.change >= 0 ? '+'+item.change : item.change }}</td>
                        <td class="text-center">{{ item.new_quantity }}</td>
                    </template>

                    <template v-if="item.type === CHANGELOG_TYPE_REPLACEMENT_DELIVERY">
                        <td class="bg-default text-center" title="Ersatzlieferung">EL</td>
                        <td class="text-center" v-bind:class="{ 'text-success': (item.change >= 0), 'text-danger': (item.change < 0) }"><i>{{ item.change >= 0 ? '+'+item.change : item.change }}</i></td>
                        <td class="text-center">{{ item.new_quantity }}</td>
                    </template>

                    <template v-if="item.type === CHANGELOG_TYPE_OUTSOURCING">
                        <td class="bg-default text-center" title="Außenlager">AL</td>
                        <td class="text-center" v-bind:class="{ 'text-success': (item.change >= 0), 'text-danger': (item.change < 0) }"><i>{{ item.change >= 0 ? '+'+item.change : item.change }}</i></td>
                        <td class="text-center">{{ item.new_quantity }}</td>
                    </template>

                    <template v-if="item.type === CHANGELOG_TYPE_SALE_TO_THIRD_PARTIES">
                        <td class="bg-danger text-center" title="Verkauf an Fremdfirmen">VaF</td>
                        <td class="text-center" v-bind:class="{ 'text-success': (item.change >= 0), 'text-danger': (item.change < 0) }">{{ item.change >= 0 ? '+'+item.change : item.change }}</td>
                        <td class="text-center">{{ item.new_quantity }}</td>
                    </template>

                    <template v-if="item.type === CHANGELOG_TYPE_TRANSFER">
                        <td class="bg-default text-center" title="Umbuchung">UB</td>
                        <td class="text-center" v-bind:class="{ 'text-success': (item.change >= 0), 'text-danger': (item.change < 0) }">{{ item.change >= 0 ? '+'+item.change : item.change }}</td>
                        <td class="text-center">{{ item.new_quantity }}</td>
                    </template>

                    <td class="text-nowrap text-center">{{ item.unit ? item.unit.name : ''}}</td>
                    <td class="text-nowrap">{{ item.created_at | moment('DD.MM.YYYY HH:mm') }} Uhr</td>
                    <td>
                        <a v-if="item.delivery_item" v-bind:href="route('order.show', item.delivery_item.delivery.order)" target="_blank">{{ item.note ? item.note : 'Bestellung ' + item.delivery_item.delivery.order.internal_order_number }}</a>
                        <template v-else>
                            {{ item.note }}
                        </template>

                        <button v-if="editEnabled" type="button" class="btn btn-xs btn-default pull-right" @click="showChangeNoteDialog(item)"><i class="fa fa-edit"></i></button>
                    </td>
                    <td>{{ item.user.name }}</td>
                    <td>
                        <a v-if="itemIsFromCurrentMonth(item, index)" v-bind:href="route('article.quantity_changelog.delete', [article.id, item.id])" v-bind:onclick="getDeleteOnClick(item)" class="btn btn-danger btn-xs" title="Löschen">
                            <i class="fa fa-trash"></i>
                        </a>

                        <template v-if="item.type === CHANGELOG_TYPE_OUTGOING || item.type === CHANGELOG_TYPE_INCOMING">
                            <btn type="default" size="xs" @click="showFixChangeNoteDialog(item)"><i class="fa fa-undo"></i></btn>
                        </template>
                    </td>
                </tr>
            </tbody>
        </table>

        <modal v-model="changeNoteDialogIsOpen" title="Kommentar bearbeiten" ref="modal" id="modal-change-changelog-note">
            <form>
                <div class="form-group">
                    <label for="changelog_note">Kommentar</label>
                    <input type="text" class="form-control" maxlength="191" name="changelog_note" id="changelog_note" v-model="changeNoteDialogItem.note">
                </div>
            </form>
            <div slot="footer">
                <btn @click="changeNoteDialogIsOpen=false">Abbrechen</btn>
                <btn type="primary" @click="saveChangelogNote()">Speichern</btn>
            </div>
        </modal>

        <modal v-model="fixChangelogDialogIsOpen" title="Bestandsänderung korrigieren" ref="modal" id="modal-fix-changelog">
            <form method="post" v-bind:action="route('article.fix_quantity_change', article.id)" id="fixChangelogForm">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label class="control-label">zu korrigierende Änderung</label>
                            <div class="form-control-static">
                                <span class="label" v-bind:class="{ 'label-success': (fixChangelogDialogItem.type === CHANGELOG_TYPE_INCOMING), 'label-danger': (fixChangelogDialogItem.type === CHANGELOG_TYPE_OUTGOING) }">
                                    {{ fixChangelogDialogItem.type == CHANGELOG_TYPE_INCOMING ? 'WA' : 'WE' }}
                                </span>
                                &nbsp;
                                <span class="bold">{{ fixChangelogDialogItem.change }} {{ fixChangelogDialogItem.unit ? fixChangelogDialogItem.unit.name : '' }}</span>
                                &nbsp;am&nbsp;
                                <span>{{ fixChangelogDialogItem.created_at | moment('DD.MM.YYYY HH:mm') }}</span>
                                &nbsp;von&nbsp;
                                <span>{{ fixChangelogDialogItem.user.name }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="changelogChange" class="control-label">Veränderung</label>
                            <div class="input-group">
                                <div class="input-group-btn">
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="changelog-current-math">-</span> <span class="caret"></span></button>
                                    <ul class="dropdown-menu pull-left" id="changelogFixChangeDropdown">
                                        <li><a href="#" class="changelog-set-sub" @click="fixChangelogDialogChangeType='sub'">-</a></li>
                                        <li><a href="#" class="changelog-set-add" @click="fixChangelogDialogChangeType='add'">+</a></li>
                                    </ul>
                                </div>
                                <input type="hidden" name="changelogChangeType" v-bind:value="fixChangelogDialogChangeType">
                                <input class="form-control" type="text" id="changelogChange" value="" name="changelogChange" placeholder="Menge" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="changelogFixType" class="control-label">Typ der Änderung</label>
                            <select id="changelogFixType" name="changelogType" class="form-control" required>
                                <option v-bind:value="CHANGELOG_TYPE_CORRECTION" data-type="fix">Korrektur</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="changelogNote" class="control-label">Bemerkung</label>
                    <textarea class="form-control" rows="3" id="changelogNote" name="changelogNote"></textarea>
                </div>

                <input type="hidden" v-bind:value="csrf" name="_token" />
                <input type="hidden" name="changelogRelatedId" id="changelogRelatedId" v-bind:value="fixChangelogDialogItem.id" />
            </form>
            <div slot="footer">
                <btn @click="fixChangelogDialogIsOpen=false">Abbrechen</btn>
                <btn type="primary" @click="submitChangelogFixForm()">Speichern</btn>
            </div>
        </modal>
    </div>
</template>

<script>
    export default {
        props: ['article', 'items', 'editEnabled'],

        methods: {
            getDeleteOnClick(item) {
                return (item.deliveryItem) ? "return confirm('Achtung, der Eintrag wird auch aus der dazugehörigen Lieferung gelöscht!')" : '';
            },
            itemIsFromCurrentMonth(item, index) {
                return (moment().format('YYYY-MM') === moment(item.created_at).format('YYYY-MM') && index === 0);
            },
            showFixChangeNoteDialog(item) {
                this.fixChangelogDialogItem = item;
                this.fixChangelogDialogIsOpen = true;
            },
            submitChangelogFixForm() {
                document.getElementById("fixChangelogForm").submit();
            },
            showChangeNoteDialog(item) {
                this.changeNoteDialogItem = item;
                this.changeNoteDialogIsOpen = true;
            },
            saveChangelogNote() {
                let that = this;

                $.post(route('article.change_changelog_note', that.article.id), {content: that.changeNoteDialogItem.note, id: that.changeNoteDialogItem.id}).done(function (data) {
                    that.changeNoteDialogIsOpen = false;
                });
            },
            getCorrectionTooltip(item) {
                if (item.related) {
                    var tooltip = 'Korrektur für ';
                    tooltip += (item.related.type === this.CHANGELOG_TYPE_INCOMING) ? 'WE' : 'WA';
                    tooltip += ' vom ' + moment(item.related.created_at).format('DD.MM.YYYY HH:mm') + ' Uhr';
                    return tooltip;
                }
            }
        },

        data() {
            return {
                fixChangelogDialogChangeType: 'sub',
                fixChangelogDialogIsOpen: false,
                fixChangelogDialogItem: {user: {}, created_at: new Date()},

                changeNoteDialogIsOpen: false,
                changeNoteDialogItem: {id: null, note: null},

                csrf: "",

                CHANGELOG_TYPE_INCOMING: 1,
                CHANGELOG_TYPE_OUTGOING: 2,
                CHANGELOG_TYPE_CORRECTION: 3,
                CHANGELOG_TYPE_COMMENT: 6,
                CHANGELOG_TYPE_INVENTORY: 7,
                CHANGELOG_TYPE_REPLACEMENT_DELIVERY: 8,
                CHANGELOG_TYPE_OUTSOURCING: 9,
                CHANGELOG_TYPE_SALE_TO_THIRD_PARTIES: 10,
                CHANGELOG_TYPE_TRANSFER: 11,

                highlightedRowId: null
            }
        },

        mounted() {
            this.csrf = document.head.querySelector('meta[name="csrf-token"]').content
        }
    }
</script>