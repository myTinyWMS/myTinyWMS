/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

import VModal from 'vue-js-modal'
import vue2Dropzone from 'vue2-dropzone'
import 'vue2-dropzone/dist/vue2Dropzone.min.css'
import Vuex from 'vuex';
import vuexI18n from 'vuex-i18n';
import Locales from './vue-i18n-locales.generated.js';

window.Vue = require('vue');

const store = new Vuex.Store();

Vue.use(vuexI18n.plugin, store);

Vue.i18n.add('en', Locales.en);
Vue.i18n.add('de', Locales.de);

const lang = document.documentElement.lang.substr(0, 2);

const moment_lang = (lang == 'en') ? 'en-gb' : lang;

const moment = require('moment');
require('moment/locale/' + moment_lang);

Vue.use(require('vue-moment'), {
    moment
});


Vue.use(VModal);

Vue.mixin({
    methods: {
        route: route
    }
});

export const serverBus = new Vue();


// or however you determine your current app locale
moment.locale(moment_lang);
Vue.i18n.set(lang);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('z', require('./components/zondicon.vue'));
Vue.component('collapse', require('./components/Collapse.vue'));
Vue.component('dot-menu', require('./components/DotMenu.vue'));
Vue.component('article-quantity-changelog', require('./components/ArticelQuantityChangelog.vue'));
Vue.component('change-quantity-form', require('./components/ChangeQuantityForm.vue'));
Vue.component('order-form', require('./components/OrderForm.vue'));

Vue.component('dropdown', require('./components/Shared/Dropdown.vue'));
Vue.component('date-picker', require('./components/Shared/DatePicker.vue'));
Vue.component('date-picker-input', require('./components/Shared/DatePickerInput'));

Vue.component('inventory-articles', require('./components/InventoryArticles.vue'));
Vue.component('data-tables-filter', require('./components/DataTablesFilter.vue'));
Vue.component('data-tables-filter-select', require('./components/DataTablesFilterSelect.vue'));
Vue.component('global-search', require('./components/GlobalSearch.vue'));

Vue.component('invoice-status-change', require('./components/InvoiceStatusChange.vue'));
Vue.component('invoice-status-change-all', require('./components/ChangeInvoiceStatusForAll.vue'));

Vue.component('add-article-note-modal', require('./components/AddArticleNoteModal.vue'));
Vue.component('change-article-price-modal', require('./components/ChangeArticlePriceModal.vue'));
Vue.component('invoice-check-modal', require('./components/InvoiceCheckModal.vue'));
Vue.component('assign-order-message-modal', require('./components/AssignOrderMessageModal.vue'));
Vue.component('select-order-article-modal', require('./components/SelectOrderArticleModal.vue'));

Vue.component('article-group-article-list', require('./components/ArticleGroupArticleList.vue'));
Vue.component('change-quantity-article-group-form', require('./components/ChangeQuantityArticleGroupForm.vue'));

Vue.component('order-messages', require('./components/OrderMessages.vue'));

Vue.component('wysiwyg-editor', require('./components/WysiwygEditor.vue'));

Vue.component('vue-dropzone', vue2Dropzone);

window.app = new Vue({
    store,
    el: '#app',
});