const defaultConfig = require('@wordpress/scripts/config/webpack.config');

module.exports = {
    ...defaultConfig,
    entry: {
        gutenberg: './src/js/gutenberg.js'
    }
}; 