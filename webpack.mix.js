const webpack = require('webpack');
let mix = require('laravel-mix');

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

var tailwindcss = require('tailwindcss');

mix.webpackConfig({
    plugins: [
        new webpack.ContextReplacementPlugin(/moment[\\\/]locale$/, /^\.\/(en|de)$/)
    ]
});


mix
    .less('resources/less/app.less', 'public/css')
    .options({
        postCss: [
            tailwindcss('/data/www/tailwind.config.js'),
        ]
    })
    .webpackConfig({
        resolve: {
            alias: {
                '@': path.resolve('resources/js'),
            },
        },
    })

    .js('resources/js/app.js', 'public/js')
    .copy('resources/css/material-icons-outline', 'public/css')
    .copy('resources/assets/vendor/datatables/German.1.10.13.json', 'public/js/datatables')
    .copy('resources/assets/vendor/datatables/English.1.10.13.json', 'public/js/datatables')
    .copy('resources/assets/vendor/iCheck/blue.png', 'public/img')
    .copy('resources/assets/vendor/iCheck/blue@2x.png', 'public/img')
    .copy('resources/assets/logo.png', 'public/img')
    .copy('resources/assets/vendor/summernote/font', 'public/css/font')
    .copy('node_modules/font-awesome/fonts/', 'public/fonts')
    .combine([
        'resources/assets/vendor/iCheck/custom.css',
        'resources/assets/vendor/select2/select2.min.css',
        'resources/assets/vendor/summernote/summernote-lite.css',
        'resources/assets/vendor/tagify/tagify.css',
        'resources/assets/vendor/daterangepicker/daterangepicker.css',
        'resources/assets/vendor/font-awesome/css/font-awesome.min.css',
    ], 'public/css/vendor.css')
    .combine([
        'resources/assets/vendor/jquery/jquery-3.1.1.min.js',
        'resources/assets/vendor/datatables/jquery.dataTables.min.js',
        'resources/assets/vendor/datatables/dataTables.rowReorder.min.js',
        'resources/assets/vendor/datatables/dataTables.rowGroup.min.js',
        'resources/assets/vendor/chartjs/Chart.bundle.min.js',
        'resources/assets/vendor/select2/select2.full.min.js',
        'resources/assets/vendor/momenjs/moment.min.js',
        'resources/assets/vendor/daterangepicker/daterangepicker.js',
        'resources/assets/vendor/summernote/summernote-lite.js',
        'resources/assets/vendor/summernote/summernote-de-DE.js',
        'resources/assets/vendor/dropzone/dropzone.js',
        'resources/assets/vendor/iCheck/icheck.min.js',
        'resources/assets/vendor/js-cookie/js.cookie.js',
        'resources/assets/vendor/html5sortable/html5sortable.min.js',
        'resources/assets/vendor/tagify/jQuery.tagify.min.js'
    ], 'public/js/vendor.js')
    .combine([
        'resources/assets/vendor/bootswatch/cyborg.css',
        'resources/assets/vendor/font-awesome/css/font-awesome.css',
        'resources/assets/vendor/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css',
        'resources/assets/vendor/bootstrap-datepicker/css/bootstrap-datepicker3.css',
        'resources/assets/vendor/bootstrap-daterangepicker/daterangepicker.css',
        'resources/assets/vendor/iCheck/custom.css',
        'resources/assets/css/handscanner.css'
    ], 'public/css/handscanner.css')
    .combine([
        'resources/assets/vendor/jquery/jquery-3.1.1.min.js',
        'resources/assets/vendor/bootstrap/js/bootstrap.js',
        'resources/assets/vendor/select2/select2.min.js',
        'resources/assets/vendor/metisMenu/jquery.metisMenu.js',
        'resources/assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js',
        'resources/assets/vendor/bootstrap-datepicker/locales/bootstrap-datepicker.de.min.js',
        'resources/assets/vendor/bootstrap-typeahead/bootstrap3-typeahead.min.js',
        'resources/assets/vendor/momenjs/moment.min.js',
        'resources/assets/vendor/bootstrap-daterangepicker/daterangepicker.js',
        'resources/assets/vendor/iCheck/icheck.min.js',
        'resources/assets/vendor/js-cookie/js.cookie.js'
    ], 'public/js/handscanner.js')
    .version();
