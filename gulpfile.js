const { src, dest, watch, parallel, series } = require('gulp');
const browsersync = require("browser-sync").create();
const cssnano = require("cssnano");
const del = require('del');
const babel = require('gulp-babel');
const uglify = require("gulp-uglify");
const rename = require("gulp-rename");
const plumber = require("gulp-plumber");
const autoprefixer = require("autoprefixer");
const postcss = require("gulp-postcss");
const sass = require('gulp-sass');
sass.compiler = require('node-sass');
const prettier = require('gulp-prettier');
const path = require('path');

//React
const rollup = require('gulp-better-rollup');
const resolve = require('rollup-plugin-node-resolve');
const commonjs = require('rollup-plugin-commonjs');

const PLUGIN_ROOT = path.resolve();
const BUILD_DIR = PLUGIN_ROOT + "/dist";
const ALL_CSS = PLUGIN_ROOT + "/assets/**/*.scss";
const FINAL_CSS = PLUGIN_ROOT + "/assets/style.scss";
const FINAL_JS  = ["!" + PLUGIN_ROOT + "dist/main.min.js", PLUGIN_ROOT+ "/assets/main.js"];
const ALL_JS = PLUGIN_ROOT + "/assets/**/*.js";

//browser sync
function browserSync(done) {
  browsersync.init({
    proxy: "bc3.test",
    files: [ALL_CSS, ALL_JS]
  });
  done();
}

// Delete Dist
function clean() {
  return del([BUILD_DIR]);
}

//Minify JS
function buildJS() {
  return src(FINAL_JS)             
    .pipe(rollup({ plugins: [babel({presets: ['@babel/react']}), resolve(), commonjs()] }, 'umd'))     
    .pipe(babel({
      presets: ['@babel/env']
    }))  
    .pipe(uglify())
    .pipe(rename({ suffix: ".min" }))     
    .pipe(dest(BUILD_DIR))
    .pipe(browsersync.stream())
}


//Minify CSS
function buildCSS() {
  return src([FINAL_CSS])
    .pipe(plumber())
    .pipe(sass().on('error', sass.logError))   
    .pipe(rename({ suffix: ".min" })) 
    .pipe(postcss([autoprefixer(), cssnano()]))
    .pipe(dest(BUILD_DIR))
}

//Watch JS and CSS
function watchAssets() {
  watch(ALL_CSS, buildCSS);
  watch(ALL_JS, buildJS);
}

function prettyJS() {
  return src(ALL_JS)
  .pipe(prettier(undefined, { filter: true }))
  .pipe(dest(file => file.base))
}

//exports
exports.watch = parallel(watchAssets, browserSync);
exports.build = series(clean, buildCSS, buildJS);
exports.prettify = prettyJS;    