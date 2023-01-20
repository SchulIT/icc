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
    .addEntry('message-overview', './assets/js/message-overview.js')
    .addEntry('zipper', './assets/js/zipper.js')
    .addEntry('display', './assets/js/display.js')
    .addEntry('book', './assets/js/book.js')
    .addEntry('entry', './assets/js/entry.js')
    .addEntry('export-book', './assets/js/export-book.js')
    .addEntry('close-confirm', './assets/js/close-confirm.js')
    .addStyleEntry('simple', './vendor/schulit/common-bundle/Resources/assets/css/simple.scss')
    .addStyleEntry('signin', './vendor/schulit/common-bundle/Resources/assets/css/signin.scss')

    .configureBabel(() => {}, {
        useBuiltIns: 'usage',
        corejs: 3
    })

    .disableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())

    .enableSassLoader(function(options) {
        options.sassOptions.importer = GlobImporter();
    })
    .enablePostCssLoader()
    .enableVueLoader()
    .enableVersioning(Encore.isProduction())

    .addLoader(
        {
            test: /bootstrap\.native/,
            use: {
                loader: 'bootstrap.native-loader'
            }
        }
    )
;

module.exports = Encore.getWebpackConfig();
