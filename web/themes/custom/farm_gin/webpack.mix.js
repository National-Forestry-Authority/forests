// webpack.mix.js

const del = require('del');
const env = process.env.NODE_ENV || 'dev';

let mix = require('laravel-mix');

mix.sass('scss/_base.scss', 'css')
    .sass('scss/_offcanvas.scss', 'css')
    .sass('scss/tabs.scss', 'css');

mix.combine(['css/_*.css'], 'css/styles.css').then(() => {
  if (env === 'production') {
    del('css/_*.css'); // deletes the temporary files
  }
});
