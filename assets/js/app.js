require('../css/app.scss');

var $ = require('jquery');
require('bootstrap');
require('select2');
var bsCustomFileInput = require('bs-custom-file-input');

// Make jQuery available
window.$ = $;

(function($) {
    'use strict';

    $(document).ready(function() {
        bsCustomFileInput.init();

        $('[data-toggle=tooltip]').tooltip();

        $('[data-trigger=form-submit]').change(function(e) {
            $(this).closest('form').submit();
        });

        $('[data-select=select2]').each(function() {
            var $elem = $(this);
            $elem.css('width', '100%'); // make it responsive
            var options = {
                theme: 'bootstrap4'
            };

            var selectLimit = $elem.attr('data-max');

            if(typeof selectLimit !== "undefined") {
                options.maximumSelectionLength = selectLimit;
            }

            $elem.select2(options);
        });

        $('a[data-show]').on('click', function(e) {
            e.preventDefault();

            var $this = $(this);
            var $target = $($this.attr('data-show'));

            $target.show();
        });

        $('a[data-remove]').on('click', function(e) {
            e.preventDefault();

            var $this = $(this);
            var $target = $($this.attr('data-remove'));

            $target.remove();
        });
    });
})(jQuery);