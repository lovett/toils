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

const app = new Vue({
    el: '#app'
});

if (document.cookie.indexOf('TIMEZONE=') === -1) {
    const tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
    document.cookie = `TIMEZONE=${tz};`;
}

/**
 * Compensate for the fixed-position main nav when scrolling to
 * anchors.
 */
function scrollToTarget(e) {
    let destination, offset, target;

    if (e && e.target.nodeName !== 'A') {
        return;
    }

    if (e && e.target.href.indexOf('#') === -1) {
        return;
    }

    if (e) {
        destination = document.getElementById(e.target.href.split('#')[1]);
    } else {
        destination = document.getElementById(window.location.hash.replace('#', ''));
    }

    if (!destination) {
        return;
    }

    if (e) {
        e.preventDefault();
    }

    offset = document.getElementById('main-nav').getBoundingClientRect().height + 20;

    window.setTimeout(() => {
        window.scrollTo({
            top: destination.offsetTop - offset,
            behavior: "smooth"
        });
    }, 250);
}

window.addEventListener('DOMContentLoaded',  function () {
    document.addEventListener('click', scrollToTarget);

    if (window.location.hash.length > 1) {
        scrollToTarget();
    }
});
