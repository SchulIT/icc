let JSZip = require('jszip');
let JsZipUtils = require('jszip-utils');
import { saveAs } from 'file-saver';

function urlToPromise(url) {
    return new Promise(function(resolve, reject) {
        JsZipUtils.getBinaryContent(url, function (err, data) {
            if(err) {
                reject(err);
            } else {
                resolve(data);
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-action=zip]').forEach(function(el) {
        el.addEventListener('click', function(event) {
            event.preventDefault();

            let progressElemenetSelector = el.getAttribute('data-progress');
            let progressElement = document.querySelector(progressElemenetSelector);

            let filesContainerSelector = el.getAttribute('data-files');
            let filesContainer = document.querySelector(filesContainerSelector);

            let progressFilenameElement = progressElement.querySelector('[data-zip-progress-file]');
            let progressBarElement = progressElement.querySelector('[data-zip-progress-bar]');

            el.classList.add('disabled');
            progressElement.style.display = 'block';

            let zip = new JSZip();

            filesContainer.querySelectorAll('[data-zip=file]').forEach(function(zipElement) {
                let filename = zipElement.getAttribute('data-zip-filename');
                let url = zipElement.getAttribute('data-zip-url');

                zip.file(filename, urlToPromise(url), { binary: true });
            });

            zip.generateAsync({ type: 'blob' }, function(metadata) {
                progressBarElement.style.width = (metadata.percent | 0) + "%";
                progressFilenameElement = metadata.currentFile;
            }).then(function(blob) {
                saveAs(blob, 'uploads.zip');
                el.classList.remove('disabled');
                progressElement.style.display = 'none';
            });
        });
    });
});