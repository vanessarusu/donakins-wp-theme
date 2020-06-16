// based on https://css-tricks.com/gulp-for-wordpress-creating-the-tasks/

import { src, dest, watch } from 'gulp';
import yargs from 'yargs';
import sass from 'gulp-sass';
import cleanCss from 'gulp-clean-css';
import gulpif from 'gulp-if';
import postcss from 'gulp-postcss';
import sourcemaps from 'gulp-sourcemaps';
import autoprefixer from 'autoprefixer';
import livereload from 'gulp-livereload';

const PRODUCTION = yargs.argv.prod;

export const styles = () => {
  return src('src/assets/scss/bundle.scss')
    .pipe(gulpif(!PRODUCTION, sourcemaps.init()))
    .pipe(sass().on('error', sass.logError))
    .pipe(gulpif(PRODUCTION, postcss([ autoprefixer ])))
    .pipe(gulpif(PRODUCTION, cleanCss({compatibility:'ie8'})))
    .pipe(gulpif(!PRODUCTION, sourcemaps.write()))
    .pipe(dest('dist/css'))
    .pipe(livereload());
}

export const scripts = () => {
  return src('src/assets/js/scripts.js')
  .pipe(dest('dist/js'))
  .pipe(livereload());
}

export const dev = () => {
  livereload.listen();
  watch('src/assets/scss/**/*.scss', styles);
  watch('src/assets/js/**/*.js', scripts);
}