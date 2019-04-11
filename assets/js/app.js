require('../css/app.scss');

var $ = require('jquery');
require('bootstrap');

// Make jQuery available
window.$ = $;

(function($) {
    'use strict';

    $(document).ready(function() {
        $('[data-toggle=tooltip]').tooltip();
    });
})(jQuery);