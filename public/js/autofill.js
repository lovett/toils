/*var card = new Vue({
    delimiters: ['[[', ']]'],
    el: '#card',
    data: {
        visible: false,
        tooltip: "fucker",
        title: 'Dinosaurs',
        items: [
            {text: "item 1", term: "term1"},
            {text: "item 2", term: "term2"},
            {text: "item 3", term: "term3"}
        ]
    },
    filters: {
        capitalize: function (value) {
            if (!value) return '';
            value = value.toString();
            return value.charAt(0).toUpperCase() + value.slice(1);
        },
        url: function (value) {
            return 'http://google.com/?q=' + value;
        }
    },
    methods: {
        addItem: function() {
            var input = document.getElementById('itemForm');
            if (input.value !== '') {
                this.items.push({text: input.value});
                input.value = '';
            }

        },
        deleteItem: function (i) {
            this.items.splice(i, 1);
        }
    }
});
*/
