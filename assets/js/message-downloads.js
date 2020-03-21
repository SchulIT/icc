window.Dropzone = require('dropzone');
window.Dropzone.autoDiscover = false;

document.addEventListener('DOMContentLoaded', function() {
    let explorerElement = document.getElementById('explorer');

    if(explorerElement === null) {
        console.error('Explorer element is not defined.');
        return;
    }

    let dropzoneElement = document.getElementById('dropzone');

    if(dropzoneElement === null) {
        console.error('Dropzone element not present.');
        return;
    }

    let dropzone = new Dropzone(dropzoneElement, {
        url: dropzoneElement.getAttribute('data-url'),
        clickable: false,
        autoProcessQueue: true,
        createImageThumbnails: false,
        previewsContainer: "#dropzone-files",
        previewTemplate: document.getElementById('dropzone-preview').innerHTML
    });

    dropzone.on('sending', function(file, xhr, data) {
        let csrfTokenName = dropzoneElement.getAttribute('data-csrf-token-name');
        let csrfToken = dropzoneElement.getAttribute('data-csrf-token');

        if(csrfTokenName !== null && csrfToken !== null) {
            data.append(csrfTokenName, csrfToken);
        }

        if(file.fullPath) {
            data.append('path', file.fullPath);
        }
    });

    dropzone.on('success', function(file, response) {
        dropzone.removeFile(file);

        if(response.files) {
            explorerElement.innerHTML = response.files;
        }
    });
});