<script>
    module.exports = {
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
            return this.fields.split(/,\s*/).reduce(function (acc, field) {
                var capitalizedName = field[0].toUpperCase() + field.substring(1);
                acc['suggested' + capitalizedName] = null;
                acc['previous' + capitalizedName] = null;
                return acc;
            }, {});
        },

        methods: {
            fetch: function (e) {
                const self = this;

                const fetchUrl = self.url + '/' + e.target.value;

                fetch(fetchUrl, {credentials: 'same-origin'}).then(function (fetchResponse) {
                    return fetchResponse.json();
                }).then(function (jsonResponse) {
                    ['previous', 'suggested'].forEach(function (group) {
                        Object.keys(jsonResponse[group]).forEach(function (field) {
                            var dataField = group + field[0].toUpperCase() + field.substring(1);
                            if (self.hasOwnProperty(dataField)) {
                                self[dataField] = jsonResponse[group][field];
                            }
                        });
                    });
                });
            },
        }
    };
</script>
