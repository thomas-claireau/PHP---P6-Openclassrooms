module.exports = ({ options }) => ({
  plugins: {
    'postcss-sort-media-queries': {
      sort: 'desktop-first',
    },
    autoprefixer: {},
    'postcss-preset-env': {},
    'css-mqpacker': {},
    cssnano: options.dev
      ? false
      : {
        preset: [
          'default',
        ],
      },
  },
});
