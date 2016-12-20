/* global Vue */
Vue.component('searchby', {
    data: function () {
        return {
            terms: ''
        };
    },
    methods: {
        applyField: function (field) {
            this.terms = (this.terms + ' ' + field + ':').trim();
            this.$els.field.focus();
        }
    }
});
