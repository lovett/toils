<template>
    <div>
        <input type="text" v-bind:name="name" v-model="value" v-bind:class="{'form-control': 1, 'is-invalid': error}" />

        <div v-if="error" class="invalid-feedback" role="alert">{{ error }}</div>

        <autofill-hint
            v-on:set="setValue"
            v-bind:suggestion="suggestedValue"
            v-bind:previous="previousValue"
        />

        <div class="actions">
            <a href="#" @click.prevent="current()">now</a>
            <a href="#" @click.prevent="toggle($event)" v-bind:class="{hidden: isOpen}">
                more shortcuts
            </a>
        </div>


        <div class="shortcuts" v-bind:class="{hidden: !isOpen}">
            <div class="card bg-light">
                <div class="card-body">
                    <div class="pullup">
                        <button type="button" @click.prevent="toggle($event)" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <p v-if="pickableGroups.includes('relweek')">
                        <strong>Relative Week</strong>
                        <a @click.prevent="relativeWeekStart(0)" href="#">start of this week</a>
                        <a @click.prevent="relativeWeekEnd(0)" href="#">end of this week</a>
                        <a @click.prevent="relativeWeekStart(-1)" href="#">start of last week</a>
                        <a @click.prevent="relativeWeekEnd(-1)" href="#">end of last week</a>
                    </p>

                    <p v-if="pickableGroups.includes('relmonth')">
                        <strong>Relative Month</strong>
                        <a @click.prevent="relativeMonthStart(0)" href="#">start of this month</a>
                        <a @click.prevent="relativeMonthEnd(0)" href="#">end of this month</a>
                        <a @click.prevent="relativeMonthStart(-1)" href="#">start of last month</a>
                        <a @click.prevent="relativeMonthEnd(-1)" href="#">end of last month</a>
                    </p>

                    <p v-if="pickableGroups.includes('relday')">
                        <strong>Relative Day</strong>
                        <a v-bind:class="{active: daysAgo == 0}" @click.prevent="relativeDay(0)" href="#">today</a>
                        <a v-bind:class="{active: daysAgo == 1}" @click.prevent="relativeDay(-1)" href="#">yesterday</a>
                        <a v-bind:class="{active: daysAgo == 2}" @click.prevent="relativeDay(-2)" href="#">2 days ago</a>
                        <a v-bind:class="{active: daysAgo == 3}" @click.prevent="relativeDay(-3)" href="#">3 days ago</a>
                    </p>

                    <p v-if="pickableGroups.includes('month')">
                        <strong>Month</strong>
                        <a v-bind:class="{active: m == pickedDate.month() + 1}" v-for="m in 12" @click.prevent="month(m)" href="#">{{ m }}</a>
                    </p>

                    <p v-if="pickableGroups.includes('day')">
                        <strong>Day</strong>
                        <a v-bind:class="{active: d == pickedDate.date()}" v-for="d in 31" @click.prevent="day(d)" href="#">{{ d }}</a>
                    </p>

                    <p v-if="pickableGroups.includes('year')">
                        <strong>Year</strong>
                        <a v-bind:class="{active: pickedDate.year() == lastYear.year()}" @click.prevent="year(lastYear.year())" href="#">{{ lastYear.year() }}</a>
                        <a v-bind:class="{active: pickedDate.year() == now.year()}" @click.prevent="year(now.year())" href="#">{{ now.year() }}</a>
                        <a v-bind:class="{active: pickedDate.year() == nextYear.year()}" @click.prevent="year(nextYear.year())" href="#">{{ nextYear.year() }}</a>
                    </p>

                    <p v-if="pickableGroups.includes('time')">
                        <strong>Hour</strong>
                        <a v-bind:class="{active: pickedDate.hour() == h || pickedDate.hour() - 12 == h}" v-for="h in 12" @click.prevent="hour(h)" href="#">{{ h }}</a>
                    </p>

                    <p v-if="pickableGroups.includes('time')">
                        <strong>Minute</strong>
                        <a @click.prevent="minute(0)" href="#">00</a>
                        <a v-bind:class="{active: pickedDate.minute() == m}" v-for="m in 59" v-if="m % 5 === 0" @click.prevent="minute(m)" href="#">
                            {{ (m < 10) ? '0' + m : m }}
                        </a>
                    </p>
                    <p v-if="pickableGroups.includes('time')">
                        <strong>Time of Day</strong>
                        <a v-bind:class="{active: pickedDate.hour() < 12}" @click.prevent="meridiem('AM')" href="#">AM</a>
                        <a v-bind:class="{active: pickedDate.hour() > 11}" @click.prevent="meridiem('PM')" href="#">PM</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
    .hidden {
        display: none;
    }

    .actions {
        font-size: .75em;
        text-transform: uppercase;
        padding: 1em 0;
    }

    .actions A {
        margin-right: 1em;
    }

    .shortcuts {
        position: relative;
        margin-top: 1em;
    }

    .shortcuts strong {
        display: block;
    }

    .shortcuts .close {
        padding: .5em;
    }


    .shortcuts a {
        display: inline-block;
        margin-right: 1em;
        padding: 0 .5em;
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

    .shortcuts .pullup {
        position: absolute;
        top: .25em;
        right: .25em;
    }

    .shortcuts .pullup a {
        font-size: 0.85em;
        margin: 0;
    }

    .shortcuts .pullup a:hover {
        color: inherit;
        background-color: inherit;
        text-decoration: underline;
    }

    .shortcuts .well {
        margin-bottom: 0;
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

            toggle: function () {
                this.isOpen = !this.isOpen;
            },

            current: function () {
                this.pickedDate = moment();
            },

            day: function (val) {
                this.pickedDate = moment(this.pickedDate).date(val);
            },

            month: function (val) {
                this.pickedDate = moment(this.pickedDate).month(val - 1);
            },

            year: function (val) {
                this.pickedDate = moment(this.pickedDate).year(val);
            },

            hour: function (val) {
                if (this.pickedDate.hour() > 12) {
                    val += 12;
                }
                this.pickedDate = moment(this.pickedDate).hour(val);
            },

            minute: function (val) {
                this.pickedDate = moment(this.pickedDate).minute(val);
            },

            meridiem: function (val) {
                const lcVal = val.toLowerCase();
                const pickedHour = this.pickedDate.hour()
                if (lcVal === 'pm' && pickedHour < 12) {
                    this.pickedDate = moment(this.pickedDate).hour(pickedHour + 12);
                }

                if (lcVal === 'am' && pickedHour > 12) {
                    this.pickedDate = moment(this.pickedDate).hour(pickedHour - 12);
                }
            },

            relativeDay: function (val) {
                this.pickedDate = moment().add(val, 'days');
            },

            relativeYear: function (val) {
                this.pickedDate = moment().add(val, 'years');
            },

            relativeWeekStart: function (val) {
                this.pickedDate = moment().startOf('week').add(val * 7, 'days');
            },

            relativeWeekEnd: function (val) {
                this.pickedDate = moment().endOf('week').add(val * 7, 'days');
            },

            relativeMonthStart: function (val) {
                this.pickedDate = moment().startOf('month').add(val, 'months');
            },

            relativeMonthEnd: function (val) {
                this.pickedDate = moment().endOf('month').add(val, 'months');
            }
        }
    }
</script>
