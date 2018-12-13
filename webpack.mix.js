const mix = require('laravel-mix');

mix.browserSync({
    notify: false,
    online: true,
    open: false,
    port: 3010,
    proxy: {
        target: 'localhost:8083',
        reqHeaders: function() {
            return {
                host: 'localhost:3010'
            };
        }
    }
});

mix.disableNotifications();

mix.js('resources/js/app.js', 'public/js');
mix.sass('resources/sass/app.scss', 'public/css');
