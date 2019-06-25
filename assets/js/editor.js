import SimpleMDE from 'simplemde';
require('../../node_modules/inline-attachment/src/inline-attachment');
require('../../node_modules/inline-attachment/src/codemirror-4.inline-attachment');

document.addEventListener('DOMContentLoaded', function(event) {
    document.querySelectorAll('[data-editor=markdown]').forEach(function(el) {
        if(el.classList.contains('md-textarea-hidden')) {
            /*
             * Somehow, when adding the markdown editor to an textarea,
             * this event is triggered again (with the hidden textarea)...
             */
            return;
        }

        if(el.getAttribute('data-preview') === null) {
            console.error('You must provide an URL which returns the markdown preview');
            return;
        }

        let previewUrl = el.getAttribute('data-preview');

        let options = {
            autoDownloadFontAwesome: false,
            autofocus: false,
            autosave: {
                enabled: false
            },
            element: el,
            placeholder: '',
            previewRender: function(text, preview) {
                let request = new XMLHttpRequest();
                request.open('POST', previewUrl, true);
                request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');

                request.onload = function() {
                    if(request.status >= 200 && request.status < 400) {
                        preview.innerHTML = request.responseText;
                    }
                };

                request.send(text);

                return 'Laden...';
            },
            spellChecker: false,
            status: false
        };

        let editor = new SimpleMDE(options);

        if(el.getAttribute('data-upload') !== null) {
            if (el.getAttribute('data-upload') === "true" || el.getAttribute('data-upload') === 'data-upload') {
                var inlineAttachmentConfig = {
                    uploadUrl: el.getAttribute('data-url')
                };

                inlineAttachment.editors.codemirror4.attach(editor.codemirror, inlineAttachmentConfig);
            }
        }
    });
});
