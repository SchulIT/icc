let JSZip = require('jszip');
let JsZipUtils = require('jszip-utils');
let axios = require('axios');
import { saveAs } from 'file-saver';

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('download').addEventListener('click', async function(event) {
        event.preventDefault();

        if(this.classList.contains('disabled')) {
            return;
        }

        this.classList.add('disabled');

        let uuids = JSON.parse(this.getAttribute('data-uuids'));
        let absenceUrl = this.getAttribute('data-absence-url');
        let attachmentUrl = this.getAttribute('data-attachment-url');

        let counter = 0;
        let total = uuids.length;

        let $zipProgress = document.querySelector('.zip-progress');
        let $progressBar = document.querySelector('.zip-progress [role=progressbar]');
        let $progressText = document.querySelector('.zip-progress > .progress-text');

        let zip = new JSZip();

        $zipProgress.classList.remove('hide');

        for(let uuid of uuids) {
            // Progress
            let progress = counter / total * 100;
            $progressBar.style.width = progress + '%';
            $progressText.innerHTML = $progressText.getAttribute('data-template').replace('%current%', counter).replace('%count%', total);

            // retrieve data
            let response = await axios.get(absenceUrl.replace('uuid', uuid));
            let absence = response.data;

            let folder = absence.student.lastname + '_'
                + absence.student.firstname + '_'
                + absence.student.external_id + '_'
                + absence.student.uuid + '/'
                + absence.from.date + '_'
                + absence.uuid + '/';

            zip.file(folder + 'index.txt', absence.metadata);

            for(let attachment of absence.attachments) {
                try {
                    let blob = await JsZipUtils.getBinaryContent(attachmentUrl.replace('uuid', attachment.uuid));
                    zip.file(folder + attachment.filename, blob, {binary: true});
                } catch {
                    zip.file(folder + attachment.filename + '.ERROR', '');
                }
            }

            counter++;
        }

        $progressText.innerHTML = $progressText.getAttribute('data-template').replace('%current%', counter).replace('%count%', total);

        let blob = await zip.generateAsync({ type: 'blob' }, function(metadata) {
            $progressText.innerHTML = $progressText.getAttribute('data-template-zip');
            $progressBar.style.width = (metadata.percent | 0) + '%';
        });
        saveAs(blob, 'absences.zip');

        console.log(this);
        this.classList.remove('disabled');
        $zipProgress.classList.add('hide');
    });
});