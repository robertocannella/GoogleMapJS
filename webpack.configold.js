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
                test: /\.(js|jsx)$/, // Apply the loader to .js files
                exclude: /node_modules/, // Don't apply the loader to node_modules
            },
            // Add more loaders for CSS, images, etc.
        ],
    },

// other configuration options...
};
