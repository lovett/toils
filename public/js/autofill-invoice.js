/* global Vue */
new Vue({
    el: '.invoice-form',
    props: {
    },

    data: {},

    created: function () {
        self = this;
        vueBus.$on('menu-changed', function (e) {
            var params = {
                projectId: e.target.value,
                limit: 1
            }
            self.fetch(params);
        });
    },

    methods: {
        fetch: function (params) {
            var resource = this.$resource('/invoice');
            resource.query(params).then((response) => {
                console.log(response);
            }, (response) => {
                console.warn(response);
            });
        }
    }
});
