<template>
    <div>
        <input v-bind:type="inputType" v-bind:name="name" v-model="value" v-bind:class="{'form-control': 1, 'is-invalid': error}" />

        <div v-if="error" class="invalid-feedback" role="alert">{{ error }}</div>

        <autofill-hint
            v-on:set="setValue"
            v-bind:suggestion="suggestedValue"
            v-bind:previous="previousValue"
        />

        <div class="shortcuts">
            <p v-if="pickableGroups.includes('reltime')">
                <a @click.prevent="relativeTime(0)" href="#">Now</a>
                <a @click.prevent="relativeTime(-1)" href="#">-1 hour</a>
                <a @click.prevent="relativeTime(1)" href="#">+1 hour</a>
            </p>

            <p v-if="pickableGroups.includes('relday')">
                <a @click.prevent="relativeDay(0)" href="#">Today</a>
                <a @click.prevent="relativeDay(-1)" href="#">-1 day</a>
                <a @click.prevent="relativeDay(1)" href="#">+1 day</a>
            </p>

            <p v-if="pickableGroups.includes('relmonth-start')">
                <a @click.prevent="relativeDay(0)" href="#">Today</a>
                <a @click.prevent="relativeMonthStart(0)" href="#">start of month</a>
                <a @click.prevent="relativeMonthStart(-1)" href="#">start of last month</a>
                <a @click.prevent="relativeWeekStart(0)" href="#">start of week</a>
                <a @click.prevent="relativeWeekStart(-1)" href="#">start of last week</a>
            </p>

            <p v-if="pickableGroups.includes('relmonth-end')">
                <a @click.prevent="relativeDay(0)" href="#">Today</a>
                <a @click.prevent="relativeMonthEnd(0)" href="#">end of month</a>
                <a @click.prevent="relativeMonthEnd(-1)" href="#">end of last month</a>
                <a @click.prevent="relativeWeekEnd(0)" href="#">end of week</a>
                <a @click.prevent="relativeWeekEnd(-1)" href="#">end of last week</a>
            </p>
        </div>
    </div>
</template>

<style scoped>
    .form-control {
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
    }

    .shortcuts {
        position: relative;
        font-size: .85em;
        padding-top: .5em;
    }

    .shortcuts a {
        display: inline-block;
        margin-right: .75em;
        padding: 0 .25em;
        text-decoration: none;
        transition: all .25s;
    }

    .shortcuts a:hover {
        color: #fff;
        background-color: #333;
    }

    .shortcuts .active {
        background-color: #333;
        color: white;
    }
</style>
<script>
    const moment = require('moment');

    module.exports = {
        props: {
            format: {
                type: String,
                default: 'YYYY-MM-DD'
            },
            groups: {
                type: String,
                default: ''
            },
            initialValue: {
                type: String,
                default: ''
            },
            inputType: {
                type: String,
                default: 'text'
            },
            previousValue: {
                type: String,
                default: '',
            },
            suggestedValue: {
                type: String,
                default: ''
            },
            name: {
                type: String
            },
            error: {
                type: String
            }
        },

        data: function () {
            let value = null;
            let initial = moment(this.initialValue, this.format, true);

            if (!initial.isValid()) {
                initial = moment();
            }

            if (this.initialValue) {
                value = initial.format(this.format);
            }

            const groupList = this.groups.split(',');

            return {
                isOpen: false,
                now: moment(),
                pristine: true,
                lastYear: moment().subtract(1, 'year'),
                nextYear: moment().add(1, 'year'),
                pickableGroups: groupList,
                pickedDate: initial,
                daysAgo: moment().diff(initial, 'days'),
                value: value
            };
        },

        watch: {
            value: function () {
                const d = moment(this.value, this.format, true);
                if (d.isValid()) {
                    this.pickedDate = d;
                }
            },

            pickedDate: function () {
                this.value = this.pickedDate.format(this.format);
                this.daysAgo = moment().diff(this.pickedDate, 'days');
            }
        },

        methods: {
            setValue: function (value) {
                this.value = value;
            },

            current: function () {
                this.pickedDate = moment();
            },

            relativeTime: function (val) {
                let base = moment();
                if (val !== 0) {
                    base = moment(this.pickedDate);
                }

                this.pickedDate = base.add(val, 'hours');
                this.pristine = false;
            },

            relativeDay: function (val) {
                let base = moment();
                if (val !== 0) {
                    base = moment(this.pickedDate);
                }

                this.pickedDate = base.add(val, 'days');
                this.pristine = false;
            },

            relativeYear: function (val) {
                this.pickedDate = moment().add(val, 'years');
                this.pristine = false;
            },

            relativeMonthStart: function (val) {
                this.pickedDate = moment().startOf('month').add(val, 'months');
                this.pristine = false;
            },

            relativeMonthEnd: function (val) {
                this.pickedDate = moment().endOf('month').add(val, 'months');
                this.pristine = false;
            },

            relativeWeekStart: function (val) {
                this.pickedDate = moment().startOf('week').add(val, 'weeks');
                this.pristine = false;
            },

            relativeWeekEnd: function (val) {
                this.pickedDate = moment().endOf('week').add(val, 'weeks');
                this.pristine = false;
            }
        },

        filters: {
            monthName: function (val) {
                return moment(val, 'MM').format('MMM');
            }
        }
    }
</script>
