const Encore = require('@symfony/webpack-encore');
const GlobImporter = require('node-sass-glob-importer');
const NodePolyfillPlugin = require('node-polyfill-webpack-plugin');

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
    .addEntry('gradebook', './assets/js/gradebook.js')
    .addEntry('tuition-choice-filter', './assets/js/tuition-choice-filter.js')
    .addStyleEntry('email', './assets/css/email.scss')
    .addStyleEntry('simple', './assets/css/simple.scss')
    .addEntry('export-student-absences', './assets/js/export-student-absences.js')
    .addEntry('parents_day', './assets/js/parents_day.js')
    .addEntry('chat', './assets/js/chat.js')

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

    .addPlugin(new NodePolyfillPlugin())
;

module.exports = Encore.getWebpackConfig();

module.exports.resolve.fallback = {
    fs: require.resolve('browserify-fs')
};