var Encore = require('@symfony/webpack-encore');
var CopyPlugin = require('copy-webpack-plugin');
const GlobImporter = require('node-sass-glob-importer');

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Add 1 entry for each "page" of your app
     * (including one that's included on every page - e.g. "app")
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if you JavaScript imports CSS.
     */
    .addEntry('app', './assets/js/app.js')
    .addEntry('collection', './assets/js/collection.js')
    .addEntry('editor', './assets/js/editor.js')
    .addEntry('timetable', './assets/css/timetable.scss')
    .addEntry('picker', './assets/js/picker.js')
    .addEntry('modal', './assets/js/modal.js')
    .addEntry('appointments', './assets/js/appointments.js')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    //.splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .disableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    //.enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    // .enableVersioning(Encore.isProduction())

    // enables Sass/SCSS support
    .enableSassLoader(function(options) {
        options.importer = GlobImporter();
    })

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes()

    // uncomment if you're having problems with a jQuery plugin
    //.autoProvidejQuery()

    // uncomment if you use API Platform Admin (composer req api-admin)
    //.enableReactPreset()
    //.addEntry('admin', './assets/js/admin.js')

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
