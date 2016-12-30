/* global Vue */
Vue.component('autofillHint', {
    props: ['value', 'target'],
    template: '<small class="help-block"><span v-if="value">Previously:</span> <a href="#" v-text="value" v-on:click.prevent="apply"></a></small>',
    data: function () {
        return {}
    },

    methods: {
        apply: function () {
            $(this.target).val(this.value);
        }
    }
});
