const path = require('path');

module.exports = {
  entry: {
    forum: './js/src/forum/index.js',
    admin: './js/src/admin/index.js',
  },
  output: {
    path: path.resolve(__dirname, 'js/dist'),
    filename: '[name].js',
  },
  mode: process.env.NODE_ENV || 'development',
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: 'babel-loader',
      },
    ],
  },
  resolve: {
    extensions: ['.js'],
  },
  externals: function({context, request}, callback) {
    if (/^flarum/.test(request)) {
      return callback(null, 'flarum.core.compat[' + JSON.stringify(request) + ']');
    }
    callback();
  },
};