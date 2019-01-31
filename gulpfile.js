/**
Mostly derived from https://bitsofco.de/a-simple-gulp-workflow
npm install gulp
npm install --save-dev gulp-sass
npm install --save-dev gulp-concat
npm install --save-dev gulp-uglify
npm install --save-dev gulp-util
npm install --save-dev gulp-rename
npm install --save-dev gulp-babel
npm install --save-dev gulp-zip
npm install --save-dev del
npm install --save-dev gulp-header
*/

// First require gulp.
var gulp   = require( 'gulp' ),
	sass   = require( 'gulp-sass' ),
	concat = require( 'gulp-concat' ),
	uglify = require( 'gulp-uglify' ),
	gutil  = require( 'gulp-util' ),
	rename = require( 'gulp-rename' ),
	header = require( 'gulp-header' ),
	zip    = require( 'gulp-zip' ),
	del    = require( 'del' );

var cssCommentBlock = "/*\nTheme Name: Vendd Child Theme\nTemplate: Vendd\nAuthor: EDD Team\nAuthor URI: https://easydigitaldownloads.com\nDescription: Vendd is a full-featured marketplace theme for Easy Digital Downloads and supporting extensions.\nVersion: 1.2.4\nLicense: GNU General Public License v2 or later\nLicense URI: http://www.gnu.org/licenses/gpl-2.0.html\nText Domain: boilerplate\nDomain Path: /languages/\nTags: one-column, two-columns, right-sidebar, accessibility-ready, custom-colors, custom-menu, featured-images, full-width-template, rtl-language-support, theme-options, threaded-comments, translation-ready, e-commerce\n*/\n";

var sassFrontendSource        = [ 'dev/scss/boilerplatechild-main-frontend.scss' ];
var sassBackendSource         = [ 'dev/scss/boilerplatechild-main-admin.scss' ];
var jsBackendSource           = [ 'dev/js/backend/*.js' ];
var jsFrontendSource          = [ 'dev/js/frontend/*.js' ];
var watcherMainFrontEndScss = gulp.watch( sassFrontendSource );
var watcherMainBackEndScss = gulp.watch( sassBackendSource );
var watcherJsFrontendSource = gulp.watch( jsFrontendSource );
var watcherJsBackendSource = gulp.watch( jsBackendSource );

// Define default task.
gulp.task( 'default', function( done ) {
	return done();
});

// Task to compile Frontend SASS file.
gulp.task( 'sassFrontendSource', function() {
	return gulp.src( sassFrontendSource )
		.pipe(sass({
			outputStyle: 'compressed'
		})
			.on( 'error', gutil.log ) )
		.pipe(concat( 'style.css' ) )
		.pipe(header(cssCommentBlock))
		.pipe(gulp.dest( './' ) )
});

// Task to compile Backend SASS file
gulp.task( 'sassBackendSource', function() {
	return gulp.src( sassBackendSource )
		.pipe(sass({
			outputStyle: 'compressed'
		})
			.on( 'error', gutil.log) )
		.pipe(gulp.dest( './' ) );
});

// Task to concatenate and uglify js files
gulp.task( 'concatAdminJs', function() {
	return gulp.src(jsBackendSource ) // use jsSources
		.pipe(concat( 'boilerplatechild_admin.min.js' ) ) // Concat to a file named 'script.js'
		.pipe(uglify() ) // Uglify concatenated file
		.pipe(gulp.dest( 'assets/js' ) ); // The destination for the concatenated and uglified file
});


// Task to concatenate and uglify js files
gulp.task( 'concatFrontendJs', function() {
	return gulp.src(jsFrontendSource ) // use jsSources
		.pipe(concat( 'boilerplatechild_frontend.min.js' ) ) // Concat to a file named 'script.js'
		.pipe(uglify() ) // Uglify concatenated file
		.pipe(gulp.dest( 'assets/js' ) ); // The destination for the concatenated and uglified file
});

gulp.task( 'copyassets', function () {
	return gulp.src([ './assets/**/*' ], {base: './'}).pipe(gulp.dest( '../boilerplatechild_dist/VenddChild-Distribution' ) );
});

gulp.task( 'copyincludes', function () {
	return gulp.src([ './includes/**/*' ], {base: './'}).pipe(gulp.dest( '../boilerplatechild_dist/VenddChild-Distribution' ) );
});

gulp.task( 'copyadminstyle', function () {
	return gulp.src([ './boilerplatechild-main-admin.css' ], {base: './'}).pipe(gulp.dest( '../boilerplatechild_dist/VenddChild-Distribution' ) );
});

gulp.task( 'copystyle', function () {
	return gulp.src([ './style.css' ], {base: './'}).pipe(gulp.dest( '../boilerplatechild_dist/VenddChild-Distribution' ) );
});

gulp.task( 'copyfunctionsfile', function () {
	return gulp.src([ './functions.php' ], {base: './'}).pipe(gulp.dest( '../boilerplatechild_dist/VenddChild-Distribution' ) );
});

gulp.task( 'zip', function () {
	return gulp.src( '../boilerplatechild_dist/VenddChild-Distribution/**' )
		.pipe(zip( 'boilerplatechild.zip' ) )
		.pipe(gulp.dest( '../boilerplatechild_dist/VenddChild-Distribution' ) );
});

gulp.task( 'cleanzip', function(cb) {
	return del([ '../boilerplatechild_dist/VenddChild-Distribution/**/*' ], {force: true}, cb);
});

gulp.task( 'clean', function(cb) {
	return del([ '../boilerplatechild_dist/VenddChild-Distribution/**/*', '!../boilerplatechild_dist/VenddChild-Distribution/boilerplatechild.zip' ], {force: true}, cb);
});

// Cleanup/Zip/Deploy task
gulp.task('default',gulp.series( 'cleanzip', 'sassFrontendSource', 'sassBackendSource', 'concatAdminJs', 'concatFrontendJs', gulp.parallel('copyassets','copyincludes', 'copystyle', 'copyadminstyle','copyfunctionsfile'),'zip','clean',function(done) {done();}));


/*
 *	WATCH TASKS FOR SCSS/CSS
 *
 */
watcherMainFrontEndScss.on('all', function(event, path, stats) {

	gulp.src( sassFrontendSource )
		.pipe(sass({
			outputStyle: 'compressed'
		})
			.on( 'error', gutil.log ) )
		.pipe(gulp.dest( 'assets/css' ) )
		.on('end', function(){ console.log('Finished!!!') });

  console.log('File ' + path + ' was ' + event + 'ed, running tasks...');
});
watcherMainBackEndScss.on('all', function(event, path, stats) {

	gulp.src( sassBackendSource )
		.pipe(sass({
			outputStyle: 'compressed'
		})
			.on( 'error', gutil.log) )
		.pipe(gulp.dest( 'assets/css' ) )
		.on('end', function(){ console.log('Finished!!!') });

  console.log('File ' + path + ' was ' + event + 'ed, running tasks...');
});
watcherJsBackendSource.on('all', function(event, path, stats) {

	gulp.src( jsBackendSource ) // use jsSources
		.pipe(concat( 'boilerplatechild_admin.min.js' ) ) // Concat to a file named 'script.js'
		.pipe(uglify() ) // Uglify concatenated file
		.pipe(gulp.dest( 'assets/js' ) )
		.on('end', function(){ console.log('Finished!!!') });


  console.log('File ' + path + ' was ' + event + 'ed, running tasks...');
});
watcherJsFrontendSource.on('all', function(event, path, stats) {

	gulp.src(jsFrontendSource ) // use jsSources
		.pipe(concat( 'boilerplatechild_frontend.min.js' ) ) // Concat to a file named 'script.js'
		.pipe(uglify() ) // Uglify concatenated file
		.pipe(gulp.dest( 'assets/js' ) ) // The destination for the concatenated and uglified file
		.on('end', function(){ console.log('Finished!!!') });

  console.log('File ' + path + ' was ' + event + 'ed, running tasks...');
});
