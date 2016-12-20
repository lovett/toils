/* global Vue */
Vue.component('autofill', {
    template: '<div>hello</div>',
    props: {
    },

    data: function () {
        return {
        }
    },

    created: function () {
        vueBus.$on('menu-changed', function (e) {
            console.log(e.target.value);
        });
    },

    methods: {
    }
});
