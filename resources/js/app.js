import Vue from 'vue'
import {BAlert, BFormFile} from 'bootstrap-vue'
import FacetSearch from './components/FacetSearch.vue';
import Autofill from './components/Autofill.vue';
import AutofillHint from './components/AutofillHint.vue';
import Pickable from './components/Pickable.vue';

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
    console.error('CSRF token meta tag not found');
}

window.Vue = require('vue');

Vue.component('facet-search', FacetSearch);
Vue.component('autofill', Autofill);
Vue.component('autofill-hint', AutofillHint);
Vue.component('pickable', Pickable);
Vue.component('b-alert', BAlert);
Vue.component('b-form-file', BFormFile);

const app = new Vue({
    el: '#app'
});

if (document.cookie.indexOf('TIMEZONE=') === -1) {
    const tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
    document.cookie = `TIMEZONE=${tz};`;
}
