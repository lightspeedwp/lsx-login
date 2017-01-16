var gulp = require('gulp');

gulp.task('default', function() {	 
	console.log('Use the following commands');
	console.log('--------------------------');
	console.log('gulp sass           to compile the style.scss to style.css');
	console.log('gulp compile-sass   to compile both of the above.');
	console.log('gulp js             to compile the custom.js to custom.min.js');
	console.log('gulp compile-js     to compile both of the above.');
	console.log('gulp watch          to continue watching all files for changes, and build when changed');
	console.log('gulp wordpress-lang to compile the lsx-login.pot');
});

var sass = require('gulp-sass');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var sort = require('gulp-sort');
var wppot = require('gulp-wp-pot');

gulp.task('sass', function () {
    gulp.src('assets/css/lsx-login.scss')
        .pipe(sass())
        .pipe(gulp.dest('assets/css/'));
});

gulp.task('js', function () {
	gulp.src('assets/js/lsx-login.js')
	.pipe(concat('lsx-login.min.js'))
	.pipe(uglify())
	.pipe(gulp.dest('assets/js'));
});
 
gulp.task('compile-sass', (['sass']));
gulp.task('compile-js', (['js']));

gulp.task('watch', function() {
	gulp.watch('assets/css/lsx-login.scss', ['sass']);
	gulp.watch('assets/js/lsx-login.js', ['js']);
});

gulp.task('wordpress-lang', function () {
	return gulp.src('**/*.php')
		.pipe(sort())
		.pipe(wppot({
			domain: 'lsx-login',
			destFile: 'lsx-login.pot',
			package: 'lsx-login',
			bugReport: 'https://bitbucket.org/feedmycode/lsx-login/issues',
			team: 'LightSpeed <webmaster@lsdev.biz>'
		}))
		.pipe(gulp.dest('languages'));
});
