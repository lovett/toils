const mix = require('laravel-mix');

mix.disableNotifications();

mix.js('resources/js/app.js', 'public/js').vue({ extractStyles: true }).version();
mix.sass('resources/sass/app.scss', 'public/css').version();

// Prevent a warning from vue-loader about dependency on prettier.
//
// The default configuration of vue-loader uses prettier in
// development mode. If that package is not installed, webpack will
// warn about an unmet dependency during development builds.
//
// What prettier does isn't especially useful for this application,
// and having an additional dependency is not desirable.
//
// Disabling prettier involves resetting test and loader portions of
// the loader rule, which seems to work fine but was mostly just a
// guesss.
mix.webpackConfig({
    module: {
        rules: [
            {
                test: /\.vue$/,
                loader: 'vue-loader',
                options: { prettify: false }
            }
        ]
    }
});
