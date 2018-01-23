<template>
    <p v-if="suggestion" class="help-block small">
        <span>Suggested:</span>
        <a href="#" v-bind:title="previousValue" v-on:click.prevent="apply">{{ suggestion }}</a>
    </p>
</template>

<script>
    module.exports = {
        props: [
            'suggestion',
            'previous',
            'fieldSelector'
        ],

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
                document.querySelector(this.fieldSelector).value = this.suggestion;
            }
        }
    };
</script>
