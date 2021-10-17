const mix = require('laravel-mix');

mix.js('app/Frontend/app.js', 'public/js').vue().version().webpackConfig({
    module: {
      rules: [
        {
          test: /\.tsx?$/,
          loader: "ts-loader",
          exclude: /node_modules/
        },
        // {
        //     test: /\.s(c|a)ss$/,
        //     use: [
        //         'vue-style-loader',
        //         'css-loader',
        //         {
        //         loader: 'sass-loader',
        //         // Requires sass-loader@^7.0.0
        //         options: {
        //             implementation: require('sass'),
        //             indentedSyntax: true // optional
        //         },
        //         // Requires >= sass-loader@^8.0.0
        //         options: {
        //             implementation: require('sass'),
        //             sassOptions: {
        //                 indentedSyntax: true // optional
        //             },
        //         },
        //         },
        //     ],
        // },
      ]
    },
    resolve: {
      extensions: ["*", ".js", ".jsx", ".vue", ".ts", ".tsx"]
    }
});
mix.sass('resources/sass/app.scss', 'public/css');
mix.disableNotifications();
// mix.browserSync({
//     proxy: {
//         target: "https://collectdev.fitsys.com",
//     }
// });

if (!mix.inProduction()) {
    mix.webpackConfig({
        devtool: 'inline-source-map'
    })
}

// mix.js('resources/js/app.js', 'public/js')
//     .sass('resources/sass/app.scss', 'public/css');
