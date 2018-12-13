/**
 * The axios HTTP library automatically handles sending the Laravel
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */
window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

window.Vue = require('vue');

Vue.component('facet-search', require('./components/FacetSearch.vue'));
Vue.component('autofill', require('./components/Autofill.vue'));
Vue.component('autofill-hint', require('./components/AutofillHint.vue'));
Vue.component('pickable', require('./components/Pickable.vue'));

const app = new Vue({
    el: '#app'
});
