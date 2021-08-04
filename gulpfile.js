const { rmSync } = require('fs');
const {series, watch, src, dest, parallel} = require('gulp');
const sass = require('gulp-sass');
const autoprefix = require('autoprefixer');
const sourcemaps = require('gulp-sourcemaps');
const postcss = require('gulp-postcss');
const cleanCSS = require('gulp-clean-css');
sass.compiler = require('node-sass');

const sassFiles = [
    //pages
    './assets/scss/pages/mainPage/mainPage.scss',
];

const autoprefixer = autoprefix({
    grid: 'autoplace'
});

const devSass = () => {
    return src(sassFiles)
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(postcss([autoprefixer]))
        .pipe(sourcemaps.write('.'))
        .pipe(dest('./public/build/css'));
};

const watchSass = async () => {
    return watch(sassFiles, devSass);
};

const buildSass = () => {
    return src(sassFiles)
        .pipe(sass().on('error', sass.logError))
        .pipe(postcss([autoprefixer]))
        .pipe(cleanCSS({
            compatibility: 'ie10'
        }))
        .pipe(dest('./public/build/css'));
};


const clearBuildDir = (cb) => {
    rmSync('./public/build', {
        force: true,
        recursive: true,
    });

    cb();
};

// const copyImages = () => {
//     return src('./assets/img/**/*')
//         .pipe(dest('./public/img'));
// };

module.exports.clear = series(clearBuildDir);
module.exports.watch = series(clearBuildDir, parallel(watchSass));
module.exports.build = series(clearBuildDir, parallel(buildSass));
module.exports.dev = series(clearBuildDir, parallel(devSass));