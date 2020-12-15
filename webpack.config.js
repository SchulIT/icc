const Encore = require('@symfony/webpack-encore');
const CopyPlugin = require('copy-webpack-plugin');
const GlobImporter = require('node-sass-glob-importer');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')

    .addEntry('app', './assets/js/app.js')
    .addEntry('collection', './assets/js/collection.js')
    .addEntry('editor', './assets/js/editor.js')
    .addEntry('timetable', './assets/css/timetable.scss')
    .addEntry('picker', './assets/js/picker.js')
    .addEntry('modal', './assets/js/modal.js')
    .addEntry('appointments', './assets/js/appointments.js')
    .addEntry('message-downloads', './assets/js/message-downloads.js')
    .addEntry('zipper', './assets/js/zipper.js')
    .addEntry('display', './assets/js/display.js')
    .addStyleEntry('simple', './vendor/schulit/common-bundle/Resources/assets/css/simple.scss')
    .addStyleEntry('signin', './vendor/schulit/common-bundle/Resources/assets/css/signin.scss')

    .disableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())

    .enableSassLoader(function(options) {
        options.importer = GlobImporter();
    })
    .enablePostCssLoader()

    .addLoader(
        {
            test: /bootstrap\.native/,
            use: {
                loader: 'bootstrap.native-loader'
            }
        }
    )
    .addPlugin(
        new CopyPlugin([
            {
                from: 'vendor/emojione/emojione/assets/png',
                to: 'emoji/png'
            },
            {
                from: 'vendor/emojione/emojione/assets/svg',
                to: 'emoji/svg'
            }
        ])
    )
;

module.exports = Encore.getWebpackConfig();
