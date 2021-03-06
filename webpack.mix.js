const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

/* mix.extract(['moment', 'pikaday'], '/js/vendor-moment.js');
mix.extract(['vue', 'livewire-vue'], '/js/vendor-vue.js'); */
//mix.extract();

mix.js('resources/js/app.js', 'public/js')
    .js('resources/js/app-vue.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css', [
        require('postcss-import'),
        require('tailwindcss'),
    ])
    .sourceMaps(true, 'source-map')
    .vue();

if (mix.inProduction()) {
    mix.version();
}

/* mix.before(() => {
    console.log(process.env.MIX_BUILD_VERSION);
}); */