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

mix
    .options({
        processCssUrls: false
    })

    .sass('resources/assets/sass/app.scss', 'public/css')
    .copy('resources/assets/vendor/bootstrap/fonts', 'public/fonts')
    .copy('resources/assets/vendor/font-awesome/fonts', 'public/fonts')
    .copy('resources/assets/vendor/ace', 'public/js/ace')
    .copy('resources/assets/vendor/datatables/German.1.10.13.json', 'public/js/datatables')
    .copy('resources/assets/vendor/datatables/English.1.10.13.json', 'public/js/datatables')
    .combine([
        'resources/assets/vendor/bootstrap/css/bootstrap.css',
        'resources/assets/vendor/animate/animate.css',
        'resources/assets/vendor/font-awesome/css/font-awesome.css',
        'resources/assets/vendor/footable/footable.bootstrap.min.css',
        'resources/assets/vendor/datatables/jquery.dataTables.min.css',
        'resources/assets/vendor/datatables/rowReorder.dataTables.min.css',
        'resources/assets/vendor/jasny/jasny-bootstrap.min.css',
        'resources/assets/vendor/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css',
        'resources/assets/vendor/select2/select2.min.css',
        'resources/assets/vendor/select2/select2-bootstrap.min.css',
        'resources/assets/vendor/bootstrap-datepicker/css/bootstrap-datepicker3.css',
    ], 'public/css/vendor.css')
    .combine([
        'resources/assets/vendor/jquery/jquery-3.1.1.min.js',
        'resources/assets/vendor/bootstrap/js/bootstrap.js',
        'resources/assets/vendor/metisMenu/jquery.metisMenu.js',
        'resources/assets/vendor/slimscroll/jquery.slimscroll.min.js',
        'resources/assets/vendor/pace/pace.min.js',
        'resources/assets/vendor/footable/footable.min.js',
        'resources/assets/vendor/datatables/jquery.dataTables.min.js',
        'resources/assets/vendor/datatables/dataTables.rowReorder.min.js',
        'resources/assets/vendor/jasny/jasny-bootstrap.min.js',
        'resources/assets/vendor/chartjs/Chart.bundle.min.js',
        'resources/assets/vendor/chartjs/Chart.PieceLabel.min.js',
        'resources/assets/vendor/chartjs-plugin-annotation/chartjs-plugin-annotation.min.js',
        'resources/assets/vendor/select2/select2.min.js',
        'resources/assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js',
        'resources/assets/vendor/bootstrap-datepicker/locales/bootstrap-datepicker.de.min.js',
        'resources/assets/vendor/bootstrap-typeahead/bootstrap3-typeahead.min.js'
    ], 'public/js/vendor.js')
    .copy('resources/assets/js/app.js', 'public/js');
