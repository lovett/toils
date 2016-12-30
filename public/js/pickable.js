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
            pickResult: this.initialValue
        }
    },

    methods: {
        toggle: function () {
            this.isOpen = !this.isOpen;
        },

        pick: function (segment, val, className, event) {
            var index, head, tail, siblings;

            if (this.format.indexOf(segment) === -1) {
                return;
            }

            if (segment === this.format) {
                this.pickResult = val;
                return;
            }

            if (Math.abs(this.pickResult.length - this.initialValue.length) === 1) {
                this.pickResult = '0' + this.pickResult;
            }

            if (Math.abs(val.length - segment.length) === 1) {
                val = '0' + val;
            }

            if (this.pickResult.length !== this.initialValue.length) {
                this.pickResult = this.initialValue;
            }

            index = this.format.indexOf(segment);
            head = this.pickResult.substr(0, index);
            tail = this.pickResult.substr(index + val.length);
            this.pickResult = head + val + tail;

            if (this.pickResult.indexOf('0') === 0) {
                this.pickResult = this.pickResult.substr(1);
            }

            siblings = event.target.parentNode.children;

            [].forEach.call(siblings, function (sibling) {
                sibling.classList.remove(className);
            });

            event.target.classList.add(className);
            $(this.target).val(this.pickResult);
        }
    }
});
