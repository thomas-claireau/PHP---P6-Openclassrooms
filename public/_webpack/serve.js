const path = require('path');
const webpack = require('webpack');
const browserSync = require('browser-sync').create();

const webpackDevMiddleware = require('webpack-dev-middleware');
const webpackHotMiddleware = require('webpack-hot-middleware');

const { publicFolder, proxyTarget, watch } = require('./config');
const webpackConfig = require('./webpack.config')({ dev: true });
const getPublicPath = require('./publicPath');

const compiler = webpack(webpackConfig);

const middleware = [
	webpackDevMiddleware(compiler, {
		publicPath: getPublicPath(publicFolder),
		// quiet: true,
		stats: { colors: true, chunck: false },
	}),
	webpackHotMiddleware(compiler, {
		log: false,
		logLevel: 'none',
	}),
];

browserSync.init({
	middleware,
	proxy: {
		target: proxyTarget,
		middleware,
	},
	contentBase: path.resolve('./src'),
	snippetOptions: {
		rule: {
			match: /<\/head>/i,
			fn: (snippet, match) => {
				return `<script src="${getPublicPath(
					publicFolder
				)}js/main.js"></script>${snippet}${match}`;
			},
		},
	},
});
