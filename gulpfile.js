/* ------------------------------------------------------------------------------
 *
 *  # Gulp file
 *
 *  Gulp tasks for Limitless template
 *
 *  Includes following tasks:
 *  # gulp lint - lints core JS files, excluding libraries
 *  # gulp sass - compiles SCSS files. Depends on variables defined below
 *  # gulp watch - watches for changes in all SCSS files and automatically recompiles them
 *  # gulp default - runs default set of tasks. Configurable by user
 *  # gulp icons - compiles icon set
 *  # gulp ckeditor - compiles ckeditor
 *
 * ---------------------------------------------------------------------------- */


// Configuration
// ------------------------------

// Define plugins
const {src, dest, watch, series} = require('gulp'),
    postcss = require('gulp-postcss'),
    autoprefixer = require('autoprefixer'),
    jshint = require('gulp-jshint'),
    compileSass = require('gulp-sass'),
    minifyCss = require('gulp-clean-css'),
    concat = require('gulp-concat'),
    rename = require('gulp-rename'),
    rtlcss = require('gulp-rtlcss'),
    del = require("del");
uglify = require('gulp-uglify');


// Setup tasks
// ------------------------------

// Lint
function jquery() {
    return src('assets/js/main/jquery.min.js')
        .pipe(uglify())
        .pipe(concat('jquery.min.js'))
        .pipe(dest('public/build/js'));
}

// Lint
function bootstrap() {
    return src('assets/js/main/bootstrap.bundle.min.js')
        .pipe(uglify())
        .pipe(concat('bootstrap.bundle.min.js'))
        .pipe(dest('public/build/js'));
}

// Lint
function customJs() {
    return src(['assets/js/custom/**/*.js'])
        .pipe(uglify())
        .pipe(dest('public/build/js/custom'));
}

function datePicker() {
    return src([
        'assets/js/plugins/ui/moment/moment.min.js',
        'assets/js/plugins/pickers/daterangepicker.js',
        'assets/js/plugins/pickers/datepicker.min.js',
        'assets/js/plugins/pickers/datepickerInit.js',
    ])
        .pipe(uglify())
        .pipe(concat('datepicker.min.js'))
        .pipe(dest('public/build/js/datepicker'));
}

function select2() {
    return src([
        'assets/js/plugins/forms/selects/**/*.js',
    ])
        .pipe(uglify())
        .pipe(concat('selects.min.js'))
        .pipe(dest('public/build/js/selects'));

}

function echarts() {
    return src([
        'assets/js/plugins/visualization/echarts/echarts.min.js',
    ])
        .pipe(uglify())
        .pipe(concat('echarts.min.js'))
        .pipe(dest('public/build/js/echarts'));

}

// Lint
function lint() {
    return src([
        'assets/js/app.js',
        'assets/js/custom.js',
        'assets/js/plugins/tables/datatables/**/*.js',
        'assets/js/plugins/editors/ckeditor/**/*.js',
    ])
        .pipe(uglify())
        .pipe(concat('all.min.js'))
        .pipe(dest('public/build/js'));
}


//
// SCSS compilation
//

// Autoprefixer config
const processors = [
    autoprefixer({
        overrideBrowserslist: [
            '>= 1%',
            'last 1 major version',
            'Chrome >= 45',
            'Firefox >= 38',
            'Edge >= 12',
            'Explorer >= 10',
            'iOS >= 9',
            'Safari >= 9',
            'Android >= 4.4',
            'Opera >= 30'
        ],
        map: false
    })
];

// Make it dynamic by changing core variables. Sensitive to location: make sure
// the paths are correct if you need to use a custom assets location
function sass() {
    return src('assets/scss/index.scss')
        .pipe(compileSass().on('error', compileSass.logError))
        .pipe(postcss(processors))
        .pipe(dest('public/build/css'))
        .pipe(minifyCss({
            level: {1: {specialComments: 0}}
        }))
        .pipe(rename({
            suffix: ".min"
        }))
        .pipe(dest('public/build/css'))
        .pipe(concat('all.min.css'))
        .pipe(dest('public/build/css'));
}

// Icons
function icons() {
    return src('assets/scss/shared/icons/**/*.scss')
        .pipe(compileSass().on('error', compileSass.logError))
        .pipe(postcss(processors))
        .pipe(minifyCss({
            level: {1: {specialComments: 0}}
        }))
        .pipe(rename({
            suffix: ".min"
        }))
        .pipe(concat('icon.min.css'))
        .pipe(dest('public/build/icons'));
}

// Icon Fonts
function iconFonts() {
    return src(['assets/css/icons/icomoon/fonts/*', 'assets/css/icons/fontawesome/fonts/*', 'assets/css/icons/material/fonts/*'])
        .pipe(dest('public/build/icons/fonts'));
}

// Fonts
function fonts() {
    return src(['assets/_fonts/Poppins/*'])
        .pipe(dest('public/build/fonts'));
}

// Images
function images() {
    return src('assets/images/*')
        .pipe(dest('public/build/images'));
}

// CKEditor
function ckeditor() {
    return src('assets/scss/layouts/layout_1/default/compile/ckeditor/*.scss')
        .pipe(compileSass().on('error', compileSass.logError))
        .pipe(postcss(processors))
        .pipe(minifyCss({
            level: {1: {specialComments: 0}}
        }))
        .pipe(dest('public/build/ckeditor/'));
}

function frontStylesMsCSS() {
    return src('assets/frontend/ms/css/*.css')
        .pipe(minifyCss({
            level: {1: {specialComments: 0}}
        }))
        .pipe(concat('ms.css'))
        .pipe(dest('public/build/frontend/ms/css/'));
}

function frontStylesMsJS() {
    return src('assets/frontend/ms/js/*.js')
        .pipe(uglify())
        .pipe(concat('ms.js'))
        .pipe(dest('public/build/frontend/ms/js/'));
}

function frontStylesTeCSS() {
    return src('assets/frontend/te/*.css')
        .pipe(minifyCss({
            level: {1: {specialComments: 0}}
        }))
        .pipe(concat('te.css'))
        .pipe(dest('public/build/frontend/te/css/'));
}

function frontStylesTeJS() {
    return src('assets/frontend/te/js/*.js')
        .pipe(uglify())
        .pipe(concat('te.js'))
        .pipe(dest('public/build/frontend/te/js/'));
}

function frontStylesTeLib() {
    return src('assets/frontend/te/lib/**/*.*')
        .pipe(dest('public/build/frontend/te/lib/'));
}

//
// Watch files for changes
//

function watchFiles() {
    watch('assets/scss/**/*.scss', series(sass));
    watch([
        'assets/js/plugins/ui/moment/moment.min.js',
        'assets/js/plugins/pickers/daterangepicker.js',
        'assets/js/plugins/pickers/datepicker.min.js',
        'assets/js/plugins/pickers/datepickerInit.js',
    ], series(datePicker));
    watch([
        'assets/js/plugins/forms/selects/**/*.js',
    ], series(select2));
    watch([
        'assets/js/plugins/visualization/echarts/echarts.min.js',
    ], series(echarts));
    watch('assets/js/custom/**/*.js', series(customJs));
    watch('assets/frontend/**/*.css', series(frontStylesMsCSS, frontStylesTeCSS));
    watch('assets/frontend/**/*.js', series(frontStylesMsJS, frontStylesTeJS, frontStylesTeLib));
    watch('assets/js/**/*.js', series(lint));
}


// Clean vendor
function clean() {
    return del(["public/build/"]);
}

//
// Default task
//

exports.default = series(clean, lint, customJs, sass, fonts, datePicker, select2, echarts, icons, iconFonts, images, ckeditor, jquery, bootstrap,frontStylesMsCSS, frontStylesMsJS, frontStylesTeCSS, frontStylesTeJS, frontStylesTeLib);


//
// Register tasks
//

exports.lint = lint;
exports.customJs = customJs;
exports.datePicker = datePicker;
exports.select2 = select2;
exports.echarts = echarts;
exports.watch = watchFiles;
exports.sass = sass;
exports.fonts = fonts;
exports.clean = clean;
exports.icons = icons;
exports.ckeditor = ckeditor;
