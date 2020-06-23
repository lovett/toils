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


            <p v-if="pickableGroups.includes('month')">
                <a v-for="m in 12" @click.prevent="month(m)" href="#">{{ m | monthName }}</a>
            </p>

            <p v-if="pickableGroups.includes('day')">
                <a v-for="d in 31" @click.prevent="day(d)" href="#">{{ d }}</a>
            </p>

            <p v-if="pickableGroups.includes('year')">
                <a @click.prevent="year(lastYear.year())" href="#">{{ lastYear.year() }}</a>
                <a @click.prevent="year(now.year())" href="#">{{ now.year() }}</a>
                <a @click.prevent="year(nextYear.year())" href="#">{{ nextYear.year() }}</a>
            </p>

            <p v-if="pickableGroups.includes('time')">
                <a v-for="h in 12" @click.prevent="hour(h)" href="#">{{ h }}</a>
            </p>

            <p v-if="pickableGroups.includes('time')">
                <a @click.prevent="minute(0)" href="#">00</a>
                <a v-for="m in 59" v-if="m % 5 === 0" @click.prevent="minute(m)" href="#">
                    {{ (m < 10) ? '0' + m : m }}
                </a>
            </p>
            <p v-if="pickableGroups.includes('time')">
                <a @click.prevent="meridiem('AM')" href="#">AM</a>
                <a @click.prevent="meridiem('PM')" href="#">PM</a>
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
            },

            useDateInput: function() {
                this.inputType = 'date';
            }

        },

        filters: {
            monthName: function (val) {
                return moment(val, 'MM').format('MMM');
            }
        }
    }
</script>
