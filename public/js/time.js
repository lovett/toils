var DatePicker = Vue.extend({
    created: function () {
	console.log('created!');
    }
});

Vue.component('date-picker', DatePicker);

new Vue({
    el: 'MAIN FORM'
});
