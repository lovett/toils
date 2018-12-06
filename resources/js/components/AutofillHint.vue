<template>
    <p v-if="suggestion" class="form-text ml-auto small">
        <span>Suggested:</span>
        <a href="#" v-bind:title="previousValue" v-on:click.prevent="apply">{{ suggestion }}</a>
    </p>
</template>

<script>
    module.exports = {
        props: {
            suggestion: {
                type: [String, Number],
                default: ''
            },
            previous: {
                type: [String, Number],
                default: ''
            },
            fieldSelector: {
                type: String,
                default: ''
            }
        },

        data: function () {
            return {}
        },

        computed: {
            previousValue: function () {
                return 'Previously: ' + this.previous;
            }
        },

        methods: {
            apply: function () {
                if (this.fieldSelector) {
                    document.querySelector(this.fieldSelector).value = this.suggestion;
                }
                this.$emit('set', this.suggestion);
            }
        }
    };
</script>
