const crypto = require('easy-web-crypto');
const axios = require('axios').default;
const papa = require('papaparse');
import { saveAs } from 'file-saver';

let progressBar = document.getElementById('progress');
let output = document.getElementById('output');

let categoryInput = document.getElementById('category');
let fileInput = document.getElementById('file');
let passwordInput = document.getElementById('password');
let convertInput = document.getElementById('convert');
let includeLessonsInput = document.getElementById('absent_lessons');

let button = document.getElementById('export');
let endpoint = button.getAttribute('data-endpoint');

let encryptedKey = JSON.parse(document.getElementById('key').value.trim());

let batchSize = 40;

button.addEventListener('click', async () => {
    let decryptedKey = null;

    if(categoryInput.value.trim() === '') {
        alert('Bitte eine Notenkategorie auswählen');
        return;
    }

    try {
        decryptedKey = await crypto.decryptMasterKey(passwordInput.value, encryptedKey);
    } catch(e) {
        alert('Passwort falsch');
        console.error(e);
        return;
    }

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

    let studentsMap = new Map();
    for(let line of lines) {
        let key = line.Vorname + "-" + line.Nachname + "-" + line.Geburtsdatum;
        studentsMap.set(key, line);
    }

    let uniqueStudents = Array.from(studentsMap.values());
    appendOutput(uniqueStudents.length + ' SuS eingelesen');

    let errors = [ ];
    let map = new Map();

    let idx = 0;
    while(idx < uniqueStudents.length) {
        let bulkRequest = {
            requests: [ ]
        };

        let start = idx;
        let end = Math.min(idx + batchSize, uniqueStudents.length);

        for(let line of uniqueStudents.slice(start, end)) {
            let request = {
                firstname: line.Vorname,
                lastname: line.Nachname,
                birthday: line.Geburtsdatum,
                year: line.Jahr,
                section: line.Abschnitt,
                grade: categoryInput.value
            };

            appendOutput('Frage Noten von ' + request.lastname +', ' + request.firstname + ' ab');
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
            } else if ('tuitions' in singleResponse) {
                for(let tuition of singleResponse.tuitions) {
                    let key = singleResponse.firstname + "-" + singleResponse.lastname + "-" + singleResponse.birthday + "-" + (tuition.subject ?? '') + "-" + (tuition.course ?? '') + "-" + (tuition.teacher ?? '');
                    map.set(key, tuition);
                }
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
        let key = line.Vorname + "-" + line.Nachname + "-" + line.Geburtsdatum + "-" + (line.Fach ?? '') + "-" + (line.Kurs ?? '') + "-" + (line.Fachlehrer ?? '');
        let data = map.get(key);

        if(data === undefined) {
            console.log('Key ' + key + ' not found');
            setProgress(100.0 * (i / lines.length));
            continue;
        }

        if(data.grade !== null) {
            lines[i].Note = await crypto.decrypt(decryptedKey, JSON.parse(data.grade));

            if(convertInput.checked) {
                lines[i].Note = convert(lines[i].Note);
            }
        }

        if(includeLessonsInput.checked) {
            lines[i]['Fehlstd.'] = data.absent_lessons;
            lines[i]['unentsch. Fehlstd.'] = data.non_excused_lessons;
        }

        setProgress(100.0 * (i / lines.length));
    }

    let csv = papa.unparse(lines, {
        delimiter: '|',
        header: true,
        columns: data.meta.fields
    });

    // download
    let file = new File([csv], 'SchuelerLeistungsdaten.dat', { type: 'text/csv' });
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
    appendOutput("⚠ " + text);
}

function convert(grade) {
    switch(grade) {
        case '15':
            return '1+'
        case '14':
            return '1'
        case '13':
            return '1-'
        case '12':
            return '2+'
        case '11':
            return '2'
        case '10':
            return '2-'
        case '9':
            return '3+'
        case '8':
            return '3'
        case '7':
            return '3-'
        case '6':
            return '4+'
        case '5':
            return '4'
        case '4':
            return '4-'
        case '3':
            return '5+'
        case '2':
            return '5'
        case '1':
            return '5-'
        case '0':
            return '6'
    }

    console.error('Ungültige Note: ' + grade);
    return grade;
}