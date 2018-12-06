<template>
    <div class="facet-search">
        <div class="input-group">
            <input type="search" ref="q" name="q" class="form-control" placeholder="Search…" v-model="facetedQuery" />
            <span class="input-group-append">
                <button class="btn" type="submit">Search</button>
            </span>
        </div>
        <ul class="list-inline facets">
            <li class="list-inline-item">Facets:</li>
            <li class="list-inline-item" v-for="facet in facetList">
                <a v-on:click="apply(facet)" href="#">{{ facet }}</a>
            </li>
        </ul>
    </div>
</template>

<style scoped>
    .facets {
        font-size: .85em;
        padding-top: .5em;
    }
</style>

<script>
    module.exports = {
        props: {
            facets: {
                type: String,
                default: ''
            },
            query: {
                type: String,
                default: ''
            }
        },
        data: function () {
            return {
                facetedQuery: this.query
            }
        },
        computed: {
            facetList: function() {
                return this.facets.split(/,\s*/);
            }
        },
        methods: {
            apply: function(facet) {
                this.facetedQuery = this.facetedQuery.replace(`${facet}:`, '').trim();
                if (this.facetedQuery.indexOf(':') === -1) {
                    this.facetedQuery = `${facet}:${this.facetedQuery}`;
                } else {
                    this.facetedQuery = `${this.facetedQuery} ${facet}:`;
                }

                this.$refs.q.focus();
            }
        }
    }
</script>
