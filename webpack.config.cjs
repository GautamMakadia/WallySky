const path = require('path');
const HtmlWebpackPlugin = require('html-webpack-plugin');


function tryResolve_(url, sourceFilename) {
  // Put require.resolve in a try/catch to avoid node-sass failing with cryptic libsass errors
  // when the importer throws
  try {
    return require.resolve(url, { paths: [path.dirname(sourceFilename)] });
  } catch (e) {
    return '';
  }
}

function tryResolveScss(url, sourceFilename) {
  // Support omission of .scss and leading _
  const normalizedUrl = url.endsWith('.scss') ? url : `${url}.scss`;
  return tryResolve_(normalizedUrl, sourceFilename) ||
    tryResolve_(path.join(path.dirname(normalizedUrl), `_${path.basename(normalizedUrl)}`),
      sourceFilename);
}

function materialImporter(url, prev) {
  if (url.startsWith('@material')) {
    const resolved = tryResolveScss(url, prev);
    return { file: resolved || url };
  }
  return { file: url };
}

module.exports = [{
  mode: 'development',

  entry: {
    index: path.resolve(__dirname, "./src/index.js"),
    about: path.resolve(__dirname, "./src/pages/about/about.js"),
    login: path.resolve(__dirname, "./src/pages/login/login.js"),
    signup: path.resolve(__dirname, "./src/pages/signup/signup.js"),
  },

  output: {
    path: path.resolve(__dirname, './dist'),
    filename: '[name].js',
    assetModuleFilename: 'images/[name]'
  },

  devServer: {
    static: {
      directory: path.resolve(__dirname, './dist'),
    },
    open: true,
    hot: true,
    compress: true,
    historyApiFallback: true,
  },

  module: {
    rules: [
      {
        test: /\.scss$/,
        use: [
          { loader: 'style-loader' },
          { loader: 'css-loader' },
          {
            loader: 'sass-loader',
            options: {
              // Prefer Dart Sass
              implementation: require('sass'),

              // See https://github.com/webpack-contrib/sass-loader/issues/804
              webpackImporter: false,
              sourceMap: true,
              sassOptions: {
                importer: materialImporter,
                includePaths: ['./node_modules']
              },
            },
          },
        ]
      },

      {
        test: /\.(png|svg|jpg|jpeg|gif)$/i,
        type: 'asset/resource',
      },
      {
        test: /\.(woff|woff2|eot|ttf|otf)$/i,
        type: 'asset/resource',
      },
    ]
  },

  plugins: [
    new HtmlWebpackPlugin({
      title: "Home",
      template: './src/index.html',
      chunks: ['index'],
      filename: 'index.html',
      minify: false
    }),

    new HtmlWebpackPlugin({
      title: "Login",
      template: './src/pages/login/login.html',
      chunks: ['login'],
      filename: 'login.html',
      minify: false
    }),


    new HtmlWebpackPlugin({
      title: "Signup",
      template: './src/pages/signup/signup.html',
      chunks: ['signup'],
      filename: 'signup.html',
      minify: false
    }),

    new HtmlWebpackPlugin({
      title: "About",
      template: './src/pages/about/about.html',
      chunks: ['about'],
      filename: 'about.html',
      minify: false
    }),

  ],


}];