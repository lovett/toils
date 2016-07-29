/* global Vue */
Vue.component('pickable', {
    props: [
        'format',
        'initial'
    ],

    data: function () {
        return {
            format: '',
            initial: '',
            pickResult: ''
        };
    },

    methods: {
        pick: function (segment, val, className, event) {
            var index, head, tail, siblings;

            if (this.format.indexOf(segment) === -1) {
                return;
            }

            if (Math.abs(this.pickResult.length - this.initial.length) === 1) {
                this.pickResult = '0' + this.pickResult;
            }

            if (Math.abs(val.length - segment.length) === 1) {
                val = '0' + val;
            }

            if (this.pickResult.length !== this.initial.length) {
                this.pickResult = this.initial;
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
        }
    }
});

new Vue({
    el: 'BODY'
});
