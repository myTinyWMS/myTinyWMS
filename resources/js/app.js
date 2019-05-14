/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

// require('./bootstrap');

import VModal from 'vue-js-modal'

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
Vue.component('add-article-modal', require('./components/AddArticleNoteModal.vue'));
Vue.component('inventory-articles', require('./components/InventoryArticles.vue'));

window.app = new Vue({
    el: '#app'
});
