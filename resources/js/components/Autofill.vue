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
                type: Object,
                default: {}
            },

            // The endpoint that will provide autofill values.
            url: {
                type: String,
                required: true
            }
        },

        data: function () {
            let data = {};

            // Convert the fields prop into a blank object.
            //
            // Each field name becomes a pair of camel-cased keys: the previous
            // value of the field, and the suggested value of the field.
            // At this point a fetch has not occurred, so their values are null.
            // Ensuring they exist gives child Pickable and AutofillHint components
            // something to bind to, so that the values can pass down to them once
            // a fetch has happened.
            for (let [key, value] of Object.entries(this.fields)) {
                // Get the field name ready for camel casing.
                const capitalizedName = key[0].toUpperCase() + key.substring(1);

                data[key] = value;
                data['suggested' + capitalizedName] = null;
                data['previous' + capitalizedName] = null;
            };

            data['hideables'] = '';

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
            reset: function () {
                const self = this;
                Object.keys(self).forEach(function (key) {
                    if (key.startsWith('previous') || key.startsWith('suggested')) {
                        self[key] = null;
                    }

                    self.hideables = '';
                });
            },

            // Make a request to the endpoint.
            fetch: function (e) {
                const self = this;

                if (e.target.value === '') {
                    return this.reset();
                    return;
                }

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

                    if (jsonResponse.hasOwnProperty('settables')) {
                        Object.keys(jsonResponse.settables).forEach(function (jsonKey) {
                            self[jsonKey] = jsonResponse.settables[jsonKey];
                        });
                    }

                    if (jsonResponse.hasOwnProperty('hideables')) {
                        self.hideables = jsonResponse['hideables'];
                    }

                }).catch(function () {
                    // Fail silently. Autofilling is a convenience rather than a necessity.
                });
            },
        }
    };
</script>
