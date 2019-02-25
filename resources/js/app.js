/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

// require('./bootstrap');

import VModal from 'vue-js-modal'

window.Vue = require('vue');

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

Vue.component('dot-menu', require('./components/DotMenu.vue'));
Vue.component('change-quantity-form', require('./components/ChangeQuantityForm.vue'));

window.app = new Vue({
    el: '#app'
});
