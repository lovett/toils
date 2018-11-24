const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

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

mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css');
