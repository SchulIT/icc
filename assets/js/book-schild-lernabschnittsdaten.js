const axios = require('axios').default;
const papa = require('papaparse');
import { saveAs } from 'file-saver';

let progressBar = document.getElementById('progress');
let output = document.getElementById('output');

let fileInput = document.getElementById('file');
let dateInput = document.getElementById('date');

let button = document.getElementById('export');
let endpoint = button.getAttribute('data-endpoint');

let batchSize = 40;

button.addEventListener('click', async () => {
    disableButton();
    clearOutput();

    appendOutput('Lese CSV-Datei ein');

    let csvInput = (await fileInput.files[0].text()).trim();
    let data = papa.parse(csvInput, {
        delimiter: '|',
        header: true,
        dynamicTyping: true
    });

    if(data.errors.length > 0) {
        console.error(data.errors);
        appendOutput('Es gab Fehler. Bitte die Javascript-Konsole prüfen (F12).')
    }

    let lines = data.data;

    appendOutput(lines.length + ' Zeilen eingelesen');

    let responses = [ ];
    let requests = [ ];
    let errors = [ ];

    let map = new Map();

    let idx = 0;
    while(idx < lines.length) {
        let bulkRequest = {
            requests: [ ]
        };

        let start = idx;
        let end = Math.min(idx + batchSize, lines.length);

        for(let line of lines.slice(start, end)) {
            let request = {
                firstname: line.Vorname,
                lastname: line.Nachname,
                birthday: line.Geburtsdatum,
                year: line.Jahr,
                section: line.Abschnitt,
                grade: line.Klasse,
                until: dateInput.value
            };

            appendOutput('Frage Anwesenheit von ' + request.lastname +', ' + request.firstname + ' ab');
            bulkRequest.requests.push(request);
        }

        appendOutput('[ ' + start + " - " + end + '] Warte, bis alle Anfragen abgeschlossen sind.');
        let response = await axios.post(endpoint, bulkRequest);

        for(let singleResponse of response.data.responses) {
            if('message' in singleResponse) {
                appendError(singleResponse.message);
                errors.push(singleResponse.message);
                appendError(singleResponse.message);

                console.error(singleResponse);
            } else {
                let key = singleResponse.firstname + "-" + singleResponse.lastname + "-" + singleResponse.birthday;
                map.set(key, singleResponse);
            }
        }

        idx += batchSize;

        if(idx > lines.length) {
            idx = lines.length;
        }

        setProgress(100.0 * (idx / lines.length));
    }

    appendOutput('Bearbeite Zeilen der SchuelerLeistungsdaten.dat');

    for(let i = 0; i < lines.length; i++) {
        let line = lines[i];
        let key = line.Vorname + "-" + line.Nachname + "-" + line.Geburtsdatum;
        let data = map.get(key);

        if(data === undefined) {
            setProgress(100.0 * (i / lines.length));
            continue;
        }

        lines[i]['SummeFehlstd'] = data.absent;
        lines[i]['SummeFehlstd_unentschuldigt'] = data.not_excused;

        setProgress(100.0 * (i / lines.length));
    }

    let csv = papa.unparse(lines, {
        delimiter: '|',
        header: true,
        columns: data.meta.fields
    });

    // download
    let file = new File([csv], 'SchuelerLernabschnittsdaten.dat', { type: 'text/csv' });
    saveAs(file);

    appendOutput('Fertig');

    for(let error of errors) {
        appendError(error);
    }

    enableButton();
});


function setProgress(percent) {
    progressBar.setAttribute('aria-valuenow', percent);
    progressBar.querySelector('.progress-bar').style.width = percent + '%';
}

function enableButton() {
    button.disabled = false;
}

function disableButton() {
    button.disabled = true;
}

function clearOutput() {
    output.innerHTML = '';
}

function appendOutput(text) {
    output.innerHTML += '<br>' + text;
}

function appendError(text) {
    appendOutput("⚠️ " + text);
}