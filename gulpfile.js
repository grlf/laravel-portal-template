var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

var bower_path = './vendor/bower_components/';

elixir.config.vendorjs = [
    'jquery/dist/jquery.js',
    'jquery-ui/jquery-ui.js',
    'jqueryui-touch-punch/jquery.ui.touch-punch.js',
    'bootstrap-sass/assets/javascripts/bootstrap.js',
    'dropzone/dist/dropzone.js',
    'sweetalert/dist/sweetalert-dev.js'
];

elixir.config.vendorcss = [
    'jquery-ui/themes/start/jquery-ui.css',
    'jquery-ui/themes/start/theme.css',
    'dropzone/dist/basic.css',
    'dropzone/dist/dropzone.css',
    'sweetalert/dist/sweetalert.css',
    'font-awesome/css/font-awesome.css'
];


elixir(function(mix) {
    mix.sass('app.scss', null, {
        includePaths : [ bower_path + 'bootstrap-sass/assets/stylesheets' ]
    });

    // Compile vendor JS and CSS
    mix.scripts(this.config.vendorjs, './public/js/vendor.js', bower_path);
    mix.styles(this.config.vendorcss, './public/css/vendor.css', bower_path);

    // Copy font files to public/fonts
    mix.copy(bower_path + 'bootstrap-sass/assets/fonts/bootstrap', 'public/fonts');
    mix.copy(bower_path + 'font-awesome/fonts', 'public/fonts');


    // Version files for long term caching and cache busting
    //mix.version(['css/app.css', 'css/vendor.css', 'js/vendor.js']);

});
