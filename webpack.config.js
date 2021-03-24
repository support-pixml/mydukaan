var path = require('path');

module.exports = {
	mode: 'development',
	entry: './react/src/App.js',
	output: {
		path: path.resolve(__dirname, 'public/dist'),
		filename: 'main.js'
	},
	module: {
		rules: [{
			test: /\.js$/,
			exclude: /node_modules/,
			use: {
				loader: 'babel-loader',
				options: {
					presets: ['@babel/preset-react']
				}
			}
		},
		{
			test: /\.(jpe?g|png|gif|woff|woff2|eot|ttf|svg)(\?[a-z0-9=.]+)?$/,
			use: {
			loader: 'url-loader?limit=100000' }
		}]
	}
}