<template>
    <div>
        <a href="#" @click.prevent="toggle($event)" v-bind:class="{hidden: isOpen}">
            shortcuts
        </a>

        <div class="shortcuts" v-bind:class="{hidden: !isOpen}">
            <div class="well">
                <div class="pullup">
                    <a href="#" @click.prevent="toggle($event)">▲</a>
                </div>

                <p v-if="pickDate">
                    <strong>Relative Week</strong>
                    <a @click.prevent="relativeWeekStart(0)" href="#">start of this week</a>
                    <a @click.prevent="relativeWeekEnd(0)" href="#">end of this week</a>
                    <a @click.prevent="relativeWeekStart(-1)" href="#">start of last week</a>
                    <a @click.prevent="relativeWeekEnd(-1)" href="#">end of last week</a>
                </p>

                <p v-if="pickDate">
                    <strong>Relative Month</strong>
                    <a @click.prevent="relativeMonthStart(0)" href="#">start of this month</a>
                    <a @click.prevent="relativeMonthEnd(0)" href="#">end of this month</a>
                    <a @click.prevent="relativeMonthStart(-1)" href="#">start of last month</a>
                    <a @click.prevent="relativeMonthEnd(-1)" href="#">end of last month</a>
                </p>

                <p v-if="pickDate">
                    <strong>Relative Day</strong>
                    <a @click.prevent="relativeDay(0)" href="#">today</a>
                    <a @click.prevent="relativeDay(-1)" href="#">yesterday</a>
                    <a @click.prevent="relativeDay(-2)" href="#">2 days ago</a>
                    <a @click.prevent="relativeDay(-3)" href="#">3 days ago</a>
                </p>

                <p v-if="pickDate">
                    <strong>Month</strong>
                    <a v-for="m in 12" @click.prevent="month(m)" href="#">{{ m }}</a>
                </p>

                <p v-if="pickDate">
                    <strong>Day</strong>
                    <a v-for="d in 31" @click.prevent="day(d)" href="#">{{ d }}</a>
                </p>

                <p v-if="pickTime">
                    <strong>Hour</strong>
                    <a v-for="h in 12" @click.prevent="hour(h)" href="#">{{ h }}</a>
                </p>

                <p v-if="pickTime">
                    <strong>Minute</strong>
                    <a @click.prevent="minute(0)" href="#">00</a>
                    <a v-for="m in 59" v-if="m % 5 === 0" @click.prevent="minute(m)" href="#">
                        {{ (m < 10) ? '0' + m : m }}
                    </a>
                </p>
                <p v-if="pickTime">
                    <strong>Time of Day</strong>
                    <a @click.prevent="meridiem('AM')" href="#">AM</a>
                    <a @click.prevent="meridiem('PM')" href="#">PM</a>
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
    .hidden {
        display: none;
    }

    .pullup {
        text-align: center;
        font-size: .85em;
    }

    .shortcuts strong {
        display: block;
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

</style>
<script>
    const moment = require('moment');
    module.exports = {
        props: {
            format: {
                type: String,
                default: 'YYYY-MM-DD'
            },
            initialValue: {
                type: [String, Object],
                default: function() {
                    return moment();
                }
            },
            fieldSelector: {
                type: String
            }
        },

        data: function () {
            return {
                isOpen: false,
                pickedDate: moment(this.initialValue),
                pickDate: this.format.indexOf('MM') > -1,
                pickTime: this.format.indexOf(':') > -1
            };
        },

        watch: {
            pickedDate: function () {
                document.querySelector(this.fieldSelector).setAttribute(
                    'value',
                    this.pickedDate.format(this.format)
                );
            }
        },

        methods: {
            toggle: function () {
                this.isOpen = !this.isOpen;
            },

            day: function (val) {
                this.pickedDate = moment(this.pickedDate).date(val);
            },

            month: function (val) {
                this.pickedDate = moment(this.pickedDate).month(val - 1);
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
