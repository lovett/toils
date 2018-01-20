require('./bootstrap');

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

//Vue.component('example-component', require('./components/ExampleComponent.vue'));
Vue.component('facet-search', require('./components/FacetSearch.vue'));

Vue.component('autofill', require('./components/Autofill.vue'));
Vue.component('autofill-hint', require('./components/AutofillHint.vue'));
Vue.component('pickable', require('./components/Pickable.vue'));

const app = new Vue({
    el: '#app'
});
