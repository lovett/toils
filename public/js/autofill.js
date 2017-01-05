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
            var capitalizedName = name[0].toUpperCase() + name.substring(1);
            acc['suggested' + capitalizedName] = null;
            acc['previous' + capitalizedName] = null;
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
            var resource, self;
            resource = this.$resource(url);
            self = this;
            resource.get().then((response) => {
                ['previous', 'suggested'].forEach(function (group) {
                    Object.keys(response.body[group]).forEach(function (field) {
                        var dataField = group + field[0].toUpperCase() + field.substring(1);
                        if (self.hasOwnProperty(dataField)) {
                            self[dataField] = response.body[group][field];
                        }
                    });
                });
            }, (response) => {
                console.warn(response);
            });
        }
    }
});
