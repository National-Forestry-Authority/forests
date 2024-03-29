const path = require('path');
const isDev = (process.env.NODE_ENV !== 'production');
const CleanWebpackPlugin = require('clean-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const FriendlyErrorsWebpackPlugin = require('friendly-errors-webpack-plugin');
const globImporter = require('node-sass-glob-importer');
const FixStyleOnlyEntriesPlugin = require('webpack-fix-style-only-entries');
const autoprefixer = require('autoprefixer');

module.exports = {
  entry: {
    styles: ['./scss/styles.scss'],
  },
  output: {
    devtoolLineToLine: true,
    path: path.resolve(__dirname, '.'),
    pathinfo: true,
    publicPath: '../',
  },
  module: {
    rules: [{
        test: /\.(config.js)$/,
        use: [
          {
            loader: 'file-loader',
            options: {
              name: '[path][name].[ext]',
              outputPath: './'
            }
          }
        ]
      },
      {
        test: /\.(png|jpe?g|gif|svg)$/,
        exclude: /sprite\.svg$/,
        use: [{
            loader: 'file-loader',
            options: {
              name: 'media/[name].[ext]?[hash]',
            },
          },
        ],
      },
      {
        test: /sprite\.svg$/,
        use: [{
            loader: 'file-loader',
            options: {
              name: 'media/[name].[ext]',
            },
          },
        ],
      },
      {
        test: /modernizrrc\.js$/,
        loader: 'expose-loader?Modernizr!webpack-modernizr-loader',
      },
      {
        test: /\.(css|sass|scss)$/,
        use: [
          {
            loader: MiniCssExtractPlugin.loader,
            options: {
              name: '[name].[ext]?[hash]',
            }
          },
          {
            loader: 'css-loader',
            options: {
              sourceMap: isDev,
              importLoaders: 2,
            },
          },
          {
            loader: 'sass-loader',
            options: {
              sourceMap: isDev,
              sassOptions: {
                importer: globImporter()
              },
            },
          },
        ],
      },
    ],
  },
  resolve: {
    modules: [
      path.join(__dirname, 'node_modules'),
    ],
    alias: {
      gin: path.resolve('build/themes/contrib/gin/styles'),
      claro: path.resolve('build/core/themes/claro/css')
    },
    extensions: ['.js', '.json'],
  },
  plugins: [
    new FriendlyErrorsWebpackPlugin(),
    new FixStyleOnlyEntriesPlugin(),
    new CleanWebpackPlugin(['dist'], {
      root: path.resolve(__dirname),
    }),
    new MiniCssExtractPlugin({
      filename: 'css/[name].css',
    }),
  ],
  watchOptions: {
    aggregateTimeout: 300,
  }
};
