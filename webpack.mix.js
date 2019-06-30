const mix = require('laravel-mix');
require('laravel-mix-tailwind');
// version does not work in hmr mode
const path = require('path');
// fix css files 404 issue

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

mix.js('resources/js/app.js', 'public/js')
  .sass('resources/sass/app.scss', 'public/css/sass.css')
  .stylus('resources/stylus/app.styl', 'public/css/stylus.css')
  .tailwind()
  .styles([
    'public/css/sass.css',
    'public/css/stylus.css',
  ], 'public/css/app.css')
  .version();
