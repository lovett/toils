/* global Vue */
Vue.component('autofillHint', {
    props: ['value', 'previous', 'target'],
    template: '<div class="help-block small"><span v-if="value">Suggested:</span> <a href="#" v-bind:title="\'Previously: \' + previous" v-text="value" v-on:click.prevent="apply"></a></div>',
    data: function () {
        return {}
    },

    methods: {
        apply: function () {
            $(this.target).val(this.value);
        }
    }
});
