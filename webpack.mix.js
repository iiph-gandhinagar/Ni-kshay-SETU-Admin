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

mix.js('resources/js/app.js', 'public/js')
    // .react()
    .postCss('resources/css/app.css', 'public/css', [
        //
    ]);

mix.js('resources/js/react-app.js', 'public/js')
    .react()
    .postCss('resources/css/react-app.css', 'public/css', [
        //
    ]);

mix
  .js(["resources/js/admin/admin.js"], "public/js")
  .sass("resources/sass/admin/admin.scss", "public/css")
  .vue();

if (mix.inProduction()) {
  mix.version();
}
