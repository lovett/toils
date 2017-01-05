/* global Vue */
Vue.component('pickable', {
    props: {
        format: {
            type: String,
            default: 'yyyy-mm-dd'
        },
        initialValue: {
            type: String,
            default: ''
        },
        opened: {
            type: Boolean,
            default: false
        },
        target: {
            type: String
        }
    },

    data: function () {
        return {
            isOpen: false,
            value: this.initialValue,
            className: null
        }
    },

    watch: {
        value: function () {
            $(this.target).val(this.value);
        }
    },

    methods: {
        toggle: function () {
            this.isOpen = !this.isOpen;
        },

        sync: function (segment) {
            var segmentParts, valueParts, self;
            segmentParts = segment.split('-');
            valueParts = this.value.split('-');
            self = this;

            [].forEach.call(segmentParts, function (part, index) {
                self.pick(part, valueParts[index]);
            });
        },

        pick: function (segment, val, event) {
            var index, head, tail, siblings;

            if (this.format.indexOf(segment) === -1) {
                return;
            }

            if (segment === this.format) {
                this.value = val;
                this.sync(segment);
                return;
            }

            if (Math.abs(this.value.length - this.initialValue.length) === 1) {
                this.value = '0' + this.value;
            }

            if (Math.abs(val.length - segment.length) === 1) {
                val = '0' + val;
            }

            if (this.value.length !== this.initialValue.length) {
                this.value = this.initialValue;
            }

            index = this.format.indexOf(segment);
            head = this.value.substr(0, index);
            tail = this.value.substr(index + val.length);
            this.value = head + val + tail;

            if (this.value.indexOf('0') === 0) {
                this.value = this.value.substr(1);
            }
        }
    }
});
