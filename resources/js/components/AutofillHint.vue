<template>
    <div v-if="suggestion"  class="card suggestion">
        <div class="card-body">
            Suggested:
            <a href="#" v-bind:title="previousValue" v-on:click.prevent="apply">{{ suggestion }}</a>
        </div>
    </div>
</template>


<style scoped>
    .suggestion {
        background-color: #eee;
        border-radius: 0;
        font-size: .85em;
    }

    .suggestion .card-body {
        padding-top: .5em;
        padding-bottom: .5em;
    }
</style>

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
