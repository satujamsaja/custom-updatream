/*
|--------------------------------------------------------------------------
| Mix Asset Management
|--------------------------------------------------------------------------
|
| Mix provides a clean, fluent API for defining some Webpack build steps
| for your application. See https://github.com/JeffreyWay/laravel-mix.
|
*/
const proxy = require('./config/proxy.js');
const mix = require('laravel-mix');
const glob = require('glob');
require('laravel-mix-stylelint');
require('laravel-mix-copy-watched');

/*
|--------------------------------------------------------------------------
| Configuration
|--------------------------------------------------------------------------
*/
mix
	.webpackConfig({
		// Use the jQuery shipped with Drupal to avoid conflicts.
		externals: {
			jquery: 'jQuery',
		},
	})
	.setPublicPath('assets')
	.disableNotifications()
	.options({
		processCssUrls: false,
	});

/*
|--------------------------------------------------------------------------
| Browsersync
|--------------------------------------------------------------------------
*/
mix.browserSync({
	proxy: proxy.proxy,
	files: [
		'assets/js/**/*.js',
		'assets/css/**/*.css',
		'assets/components/**/*.css',
		'assets/components/**/*.js',
		'src/components/**/*.css',
		'src/components/**/*.js',
		'src/components/**/*.twig',
		'templates/**/*.twig',
	],
	stream: true,
});

/*
|--------------------------------------------------------------------------
| SASS
|--------------------------------------------------------------------------
*/
mix.sass('src/scss/priority.scss', 'css');
mix.sass('src/scss/main.scss', 'css');
glob.sync('src/components/**/*.scss').forEach((sourcePath) => {
	const destinationPath = sourcePath.replace(
		/^src\/(components\/.+)\/_?(.+)\.scss$/,
		'$1/$2.css'
	);
	mix.sass(sourcePath, destinationPath);
});

/*
|--------------------------------------------------------------------------
| JS
|--------------------------------------------------------------------------
*/
mix.js('src/js/priority.js', 'js');
mix.js('src/js/main.js', 'js');
glob.sync('src/components/**/*.js').forEach((sourcePath) => {
	const destinationPath = sourcePath.replace(
		/^src\/(components\/.+)\/(.+)\.js$/,
		'$1/$2.js'
	);
	mix.js(sourcePath, destinationPath);
});

/*
|--------------------------------------------------------------------------
| Assets (images, icons, videos, fonts, etc)
|--------------------------------------------------------------------------
*/
mix.copyDirectoryWatched('src/assets/images', 'assets/images', { base: 'src/assets/images' });
mix.copyDirectoryWatched('src/assets/icons', 'assets/icons', { base: 'src/assets/icons' });
mix.copyDirectoryWatched('src/assets/videos', 'assets/videos', { base: 'src/assets/videos' });
mix.copyDirectoryWatched('src/assets/fonts', 'assets/fonts', { base: 'src/assets/fonts' });