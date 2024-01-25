const crypto = require('easy-web-crypto');
const xlsx = require('xlsx-populate');
const axios = require('axios').default;

let decryptedKey = null;
let keyPassword = null;

let isEditModeEntered = false;
let callbackRegistered = false; // Flag whether the onbeforeunload callback is already registered (to prevent registering twice)

async function exportXlsx() {
    if(decryptedKey === null) {
        return;
    }

    let $table = document.getElementById('grades');
    let sheetName = $table.getAttribute('data-worksheet');
    let workbook = await xlsx.fromBlankAsync();

    let data = [ ];
    let header = [ ];

    $table.querySelector('thead').querySelectorAll('th').forEach(function($th) {
        header.push($th.textContent.trim());
    });

    data.push(header);

    for(const $tr of $table.querySelectorAll('tbody > tr')) {
        let row = [ ];

        for(const $td of $tr.querySelectorAll('td')) {
            if($td.getAttribute('data-xlsx') === 'raw') {
                row.push($td.innerText.trim());
                continue;
            }

            if($td.getAttribute('data-xlsx') !== 'encrypted') {
                continue;
            }

            let $encryptedInput = $td.querySelector('input[data-encrypted]');

            if($encryptedInput === null) {
                row.push('');
                continue;
            }

            let encrypted = $encryptedInput.getAttribute('data-encrypted');
            if(encrypted === null || encrypted === '') {
                row.push('');
                continue;
            }

            row.push(await crypto.decrypt(decryptedKey, JSON.parse(encrypted)));
        }

        data.push(row);
    }

    workbook.sheet(0).cell('A1').value(data);
    workbook.sheet(0).name(sheetName);

    let blob = await workbook.outputAsync({ type: 'base64', password: keyPassword });

    let $a = document.createElement('a');
    document.body.appendChild($a);
    $a.href = "data:" + xlsx.MIME_TYPE + ";base64," + blob;
    $a.download = sheetName + ".xlsx";
    $a.click();
    document.body.removeChild($a);
}

async function decryptAll() {
    if(decryptedKey === null) {
        return;
    }

    for(let element of document.querySelectorAll('[data-encrypted]')) {

        let input = element;
        let select = document.querySelector(element.getAttribute('data-select'));

        if(select === null) {
            continue;
        }

        let encryptedValue = element.value;

        if(select.nodeName.toLowerCase() === 'select') {
            let form = element.closest('form');
            if(form !== null && form.getAttribute('data-keepalive-url') !== null) {
                enterEditMode(form.getAttribute('data-keepalive-url'));
            }

            select.removeAttribute('disabled');

            select.addEventListener('change', async function (element) {
                preventWindowUnload();
                let encryptedValue = '';
                if (this.value !== '') {
                    encryptedValue = JSON.stringify(await crypto.encrypt(decryptedKey, this.value));
                }

                input.value = encryptedValue;
            });

            if(encryptedValue === null || encryptedValue === '') {
                continue;
            }

            select.value = await crypto.decrypt(decryptedKey, JSON.parse(encryptedValue));
        } else {
            if(encryptedValue === null || encryptedValue === '') {
                select.innerHTML = '<span class="badge text-bg-secondary">N/A</span>';
                continue;
            }

            select.innerHTML = await crypto.decrypt(decryptedKey, JSON.parse(encryptedValue));
        }
    }
}

function enterEditMode(keepAliveUrl) {
    if(isEditModeEntered === true) {
        return;
    }

    console.info('Enter edit mode with keep alive url ' + keepAliveUrl);

    // setup keep alive
    setInterval(async () => {
        await axios.get(keepAliveUrl);
    }, 30*1000); // every 30s

    setTimeout(() => {
        alert('Bitte nun die Noten speichern, um Datenverlust zu vermeiden. Ab sofort erfolgt die Bearbeitung auf eigene Gefahr.');
    }, 15*60*1000); // after 15mins

    isEditModeEntered = true;
}

function preventWindowUnload() {
    if(callbackRegistered === true) {
        return;
    }

    window.onbeforeunload = function() {
        return '';
    }

    document.getElementById('save-caution').classList.remove('d-none');

    callbackRegistered = true;
}

document.addEventListener('DOMContentLoaded', function() {

    if(document.getElementById('generatekey') !== null) {
        document.getElementById('generatekey').addEventListener('click', async function (event) {
            event.preventDefault();
            let encryptedKey = '';
            let password = document.querySelector(this.getAttribute('data-passphrase')).value;
            try {
                encryptedKey = await crypto.genEncryptedMasterKey(password);
            } catch (e) {
                alert(e);
                console.error(e);
            }
            let target = document.querySelector(this.getAttribute('data-key'));
            target.value = JSON.stringify(encryptedKey);
        });
    }

    if(document.getElementById('password_btn') !== null) {
        document.getElementById('password_btn').addEventListener('click', async function(event) {
            event.preventDefault();

            let password = document.querySelector(this.getAttribute('data-passphrase')).value;
            let encryptedKey = JSON.parse(document.querySelector(this.getAttribute('data-key')).innerHTML.trim());
            let ttl = parseInt(document.querySelector(this.getAttribute('data-key')).getAttribute('data-ttl'));

            try {
                decryptedKey = await crypto.decryptMasterKey(password, encryptedKey);
                keyPassword = password;
                this.closest('.card-body').querySelector('.bs-callout').classList.remove('hide');
                this.closest('.input-group').remove();
            } catch (e) {
                alert('Falsches Passwort');
                console.error(e);
                return;
            }

            if(window.sessionStorage.getItem('gradebook.psk') === null && ttl > 0) {
                let expireDate = new Date((new Date).getTime() + ttl * 1000);
                let data = {
                    password: password,
                    expireDate: expireDate
                };

                window.sessionStorage.setItem('gradebook.psk', JSON.stringify(data));
            }

            // Decrypt
            await decryptAll();
        });

        if(window.sessionStorage.getItem('gradebook.psk') !== null) {
            let data = JSON.parse(window.sessionStorage.getItem('gradebook.psk'));
            let expireDate = new Date(data.expireDate);

            if(expireDate.getTime() >= (new Date()).getTime()) {
                document.querySelector(document.getElementById('password_btn').getAttribute('data-passphrase')).value = data.password;
                document.getElementById('password_btn').click();
            } else {
                window.sessionStorage.removeItem('gradebook.psk');
            }
        }
    }

    document.querySelector('#download_btn')?.addEventListener('click', async function(event) {
        event.preventDefault();

        if(decryptedKey === null) {
            alert('Bitte zuerst das Passwort eingeben');
            return;
        }

        let $caution = document.querySelector(this.getAttribute('data-caution'));
        if($caution.checked !== true) {
            alert('Bitte den Hinweis bestätigen');
            $caution.focus();
            return;
        }

        let $icon = this.querySelector('i');
        $icon.classList.remove('fa-download');
        $icon.classList.add('fa-spinner');
        $icon.classList.add('fa-spin');

        await exportXlsx();

        $icon.classList.remove('fa-spinner');
        $icon.classList.remove('fa-spin');
        $icon.classList.add('fa-download');
    });

    document.getElementById('gradeform')?.addEventListener('submit', function() {
        window.onbeforeunload = null;
    })
});