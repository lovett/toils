<script>
    module.exports = {
        props: {
            // Whether the component should self-trigger a call to the endpoint.
            //
            // This can be useful when the form has been prepopulated server-side.
            autofetch: {
                type: Boolean,
                default: false
            },

            // Whether the component should be active.
            //
            // If false, the component will deactivate. Useful when
            // functionality is desired for adds but not for edits.
            enabled: {
                type: Boolean,
                default: true
            },

            // A space-delimited list of field names participating in autofill.
            //
            // The names in this list should reflect the keys within the JSON
            // returned by the endpoint. For each name, two data keys will be
            // created: previous and suggested.
            fields: {
                type: String,
                default: ''
            },

            // The endpoint that will provide autofill values.
            url: {
                type: String,
                required: true
            }
        },

        data: function () {
            // Convert the fields prop into a blank object.
            //
            // Each field name becomes a pair of camel-cased keys: the previous
            // value of the field, and the suggested value of the field.
            // At this point a fetch has not occurred, so their values are null.
            // Ensuring they exist gives child Pickable and AutofillHint components
            // something to bind to, so that the values can pass down to them once
            // a fetch has happened.
            const data = this.fields.split(/\s+/).reduce(function (acc, field) {
                // Get the field name ready for camel casing.
                const capitalizedName = field[0].toUpperCase() + field.substring(1);

                acc['suggested' + capitalizedName] = null;
                acc['previous' + capitalizedName] = null;

                return acc;
            }, {});

            return data;
        },

        // Automatically trigger a fetch.
        //
        // If the autofetch prop is true and there is a dropdown that has been
        // marked as the autofillTrigger ref, trigger a change event on it to
        // cause a fetch.
        mounted: function () {
            if (!this.enabled) {
                return;
            }

            if (!this.autofetch) {
                return;
            }

            if (!this.$refs.hasOwnProperty('autofillTrigger')) {
                return;
            }

            this.$refs.autofillTrigger.dispatchEvent(new Event('change'));
        },

        methods: {
            // Make a request to the endpoint.
            fetch: function (e) {
                const self = this;

                const fetchUrl = self.url + '/' + e.target.value;

                fetch(fetchUrl, {credentials: 'same-origin'}).then(function (fetchResponse) {
                    return fetchResponse.json();
                }).then(function (jsonResponse) {
                    ['previous', 'suggested'].forEach(function (group) {
                        const jsonGroup = jsonResponse[group];

                        Object.keys(jsonGroup).forEach(function (jsonKey) {
                            const dataKey = group + jsonKey[0].toUpperCase() + jsonKey.substring(1);
                            self[dataKey] = jsonGroup[jsonKey];
                        });
                    });
                });
            },
        }
    };
</script>
