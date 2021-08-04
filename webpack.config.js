const path = require('path');

module.exports = {
    resolve: {
        extensions: ['.js'],
    },
    entry: {
        //base
        baseScript: path.resolve(__dirname, 'assets', 'js', 'baseScript.js'),

        //pages
        mainPage: path.resolve(__dirname, 'assets', 'js', 'mainPage.js'),
    },
    output: {
        path: path.resolve(__dirname, 'public', 'js'),
        filename: '[name].js'
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ['@babel/preset-env']
                    }
                }
            }
        ]
    },
    performance: {
        hints: false,
        maxEntrypointSize: 512000,
        maxAssetSize: 512000
    },
    devtool: 'source-map'
};