/* global Vue */
Vue.component('autofill', {
    props: {
        // The Ajax endpoint to request autofill values from.
        url: {
            type: String,
            required: true
        },

        // A comma-delimited list of field names. Field name must match key in Ajax response.
        fields: {
            type: String
        }
    },

    data: function () {
        return this.fields.split(/, ?/).reduce(function (acc, name) {
            acc[name] = null;
            return acc;
        }, {});
    },

    created: function () {
        var self = this;


        vueBus.$on('menu-changed', function (e) {
            self.fetchNewestByProject(e.target.value);
        });
    },

    methods: {
        fetchNewestByProject: function (id) {
            this.fetchNewest(this.url + '/' + id);
        },

        fetchNewest: function (url) {
            var resource = this.$resource(url);
            resource.get().then((response) => {
                Object.keys(response.body).forEach(function (field) {
                    if (this.hasOwnProperty(field) === false) {
                        return;
                    }
                    this[field] = response.body[field];
                }, this);
            }, (response) => {
                console.warn(response);
            });
        }
    }
});
