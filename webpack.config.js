const path = require('path');

module.exports = {
    entry: './src/index.js',
    output: {
        path: path.resolve(__dirname, 'build'),
        filename: 'index.js',
    },
    module: {
        rules: [
            {
                test: /\.js$/, // Apply the loader to .js files
                exclude: /node_modules/, // Don't apply the loader to node_modules
                use: {
                    loader: 'babel-loader', // Use the Babel loader
                },
            },
            // Add more loaders for CSS, images, etc.
        ],
    },

// other configuration options...
};
