require('dotenv').config();
let mix = require('laravel-mix');
let path = require('path');

mix.webpackConfig({
	resolve: {
		extensions: ['.js', '.json', '.vue'],
		alias: {
			'@': path.join(__dirname, 'resources/js')
		}
	},
	module: {
	    rules: [
		    {
		        test: /\.mp4$/,
		        use: [
		          	{
		            	loader: 'file-loader?name=videos/[name].[ext]',
		          	},
		        ],
		    },
		]
	},
	stats: {
		assets: true,
		children: false,
		chunks: false,
		errors: true,
		errorDetails: true,
		modules: false,
		timings: true,
		colors: true
	}
});
mix.js('resources/js/main.js', 'public/js').vue()
.sass('resources/sass/app.scss', 'public/css');

// Versioning
mix.version();