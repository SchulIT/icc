import '../../node_modules/bootstrap-markdown-editor-4/src/bootstrap-markdown-editor';

+function($) {
    'use strict';

    $(document).ready(function() {
        $('[data-editor=markdown]').each(function() {
            var $elem = $(this);

            if($elem.hasClass('md-textarea-hidden')) {
                /*
                 * Somehow, when adding the markdown editor to an textarea,
                 * this event is triggered again (with the hidden textarea)...
                 */
                return;
            }

            var previewUrl = $elem.attr('data-preview');

            if(typeof(previewUrl) === 'undefined') {
                console.error('You must provide an URL which returns the markdown preview');
                return;
            }

            var options = {
                preview: true,
                fullscreen: false,
                label: {
                    btnHeader1: 'Überschrift 1',
                    btnHeader2: 'Überschrift 2',
                    btnHeader3: 'Überschrift 3',
                    btnBold: 'Fett',
                    btnItalic: 'Kursiv',
                    btnList: 'Ungeordnete Liste',
                    btnOrderedList: 'Geordnete Liste',
                    btnLink: 'Link',
                    btnImage: 'Bild einfügen',
                    btnUpload: 'Bild hochladen',
                    btnEdit: 'Bearbeiten',
                    btnPreview: 'Vorschau',
                    btnFullscreen: 'Vollbild',
                    loading: 'Laden'
                },
                onPreview: function(content, callback) {
                    $.post(previewUrl, content, function onSuccess(content) {
                        callback(content);
                    }, 'html');
                }
            };

            if($elem.attr('data-upload') === "true" || $elem.attr('data-upload') === 'data-upload') {
                var url = $(this).attr('data-url');
                options.imageUpload = true;
                options.uploadPath = url;
            }

            $elem.markdownEditor(options);
        });
    });
}(jQuery);