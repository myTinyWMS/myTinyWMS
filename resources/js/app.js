/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

import VModal from 'vue-js-modal'
import vue2Dropzone from 'vue2-dropzone'
import 'vue2-dropzone/dist/vue2Dropzone.min.css'

window.Vue = require('vue');

Vue.use(require('vue-moment'));
Vue.use(VModal);

Vue.mixin({
    methods: {
        route: route
    }
});

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

Vue.component('inventory-articles', require('./components/InventoryArticles.vue'));
Vue.component('dropdown', require('./components/Shared/Dropdown.vue'));
Vue.component('data-tables-filter', require('./components/DataTablesFilter.vue'));
Vue.component('data-tables-filter-select', require('./components/DataTablesFilterSelect.vue'));
Vue.component('global-search', require('./components/GlobalSearch.vue'));

Vue.component('invoice-status-change', require('./components/InvoiceStatusChange.vue'));
Vue.component('invoice-status-change-all', require('./components/ChangeInvoiceStatusForAll.vue'));

Vue.component('add-article-note-modal', require('./components/AddArticleNoteModal.vue'));
Vue.component('change-article-price-modal', require('./components/ChangeArticlePriceModal.vue'));
Vue.component('invoice-check-modal', require('./components/InvoiceCheckModal.vue'));

Vue.component('vue-dropzone', vue2Dropzone);

window.app = new Vue({
    el: '#app',
});