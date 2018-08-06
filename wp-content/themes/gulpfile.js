/*
  constant module = require('module');
  -- TOP LEVEL FUNCTIONS --
  gulp.task - Define tasks
  gulp.src - Point to the files to use
  gulp.dest - Points to the folder to output
  gulp.watch - Watch files and folders for changes
*/

var gulp = require('gulp');
var sass = require('gulp-sass');
var watch = require('gulp-watch');
var browserSync = require('browser-sync').create();
var reload = browserSync.reload;

// Start browserSync
gulp.task('browser-sync', function() {
  browserSync.init({
    proxy: 'http://localhost:8888/chocogiftz/'
  });
});

// Logs Message (eerste task)
gulp.task('message', function(){
  return console.log('Gulp is running...');
});

// A task that actually does something
// CSS files maken van SCSS
gulp.task('sass', function(){
  gulp.src('src/scss/**/*.scss')
    .pipe(sass().on('error', sass.logError))
    .pipe(gulp.dest('storefront-child/build/css/'))
    .pipe(browserSync.stream());
});

// Watch task
gulp.task('watch', ['browser-sync','sass'], function() {
  gulp.watch('src/scss/**/*.scss', ['sass']);
});

// Default taak
// Maakt automatisch de CSS als er SCSS veranderd
gulp.task('default', ['browser-sync','sass'], function() {
  return gulp.watch('src/scss/**/*.scss', ['sass']);
});
