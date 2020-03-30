window.Dropzone = require('dropzone');
window.Dropzone.autoDiscover = false;

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-area=dropzone]').forEach(function(dropzoneEl) {
        let previewTemplate = document.querySelector(dropzoneEl.getAttribute('data-preview')).innerHTML;

        let containerElementSelector = dropzoneEl.getAttribute('data-preview-container');
        let containerElement = document.querySelector(containerElementSelector);

        let dropzone = new Dropzone(dropzoneEl, {
            url: dropzoneEl.getAttribute('data-url'),
            clickable: false,
            autoProcessQueue: true,
            createImageThumbnails: false,
            previewTemplate: previewTemplate,
            previewsContainer: containerElement
        });

        dropzone.on('sending', function(file, xhr, data) {
            let successElementSelector = dropzoneEl.getAttribute('data-success');
            if(successElementSelector !== null) {
                document.querySelector(successElementSelector).classList.add('d-none');
            }

            let csrfTokenName = dropzoneEl.getAttribute('data-csrf-token-name');
            let csrfToken = dropzoneEl.getAttribute('data-csrf-token');

            if(csrfTokenName !== null && csrfToken !== null) {
                data.append(csrfTokenName, csrfToken);
            }

            if(dropzoneEl.getAttribute('data-upload-folders') !== null && file.fullPath) {
                data.append('path', file.fullPath);
            }
        });

        dropzone.on('success', function(file, response) {
            dropzone.removeFile(file);

            let successElementSelector = dropzoneEl.getAttribute('data-success');
            if(successElementSelector !== null) {
                document.querySelector(successElementSelector).classList.remove('d-none');
            } else {
                let insertSelector = dropzoneEl.getAttribute('data-insert');

                if(insertSelector !== null && response.file) {
                    let elem = document.createElement('div');
                    elem.innerHTML = response.file;
                    document.querySelector(insertSelector).appendChild(elem);
                }
            }
        });
    });
});