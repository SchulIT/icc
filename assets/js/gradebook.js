const crypto = require('easy-web-crypto');

let cryptoAlgo = 'AES-GCM';
let encoder = new TextEncoder();
let decoder = new TextDecoder();

function decrypt(key, encryptedString) {

}

function decryptAllFields(psk) {
    document.querySelectorAll('input[data-encrypted]', function(el) {

    });
}

function encrypt(key, decryptedString) {

}

async function encryptAllFields(psk) {
    let key = await crypto.genEncryptedMasterKey(psk);
    console.log(key);
    console.log(JSON.stringify(key));


    document.querySelectorAll('input[data-encrypted]').forEach(function(el) {
        let select = document.querySelector('select[data-decrypted="' + el.getAttribute('data-select') + '"]');
        // current value
        let decryptedValue = select.value;

        if(decryptedValue === "" && el.getAttribute('data-encrypted') === "") {
            // Kein Wert ausgewählt und es gab auch noch keinen Wert -> nichts tun
            return;
        }

        //let encryptedValue = encrypt(key, decryptedValue);
        //el.value = encryptedValue;
    });

    // Continue!
}

document.addEventListener('DOMContentLoaded', function() {

    document.getElementById('gradeform').addEventListener('submit', function(event) {
        encryptAllFields(document.getElementById('password').value);
        event.preventDefault();
    });



    document.getElementById('password').addEventListener('click', function(event) {
        event.preventDefault();
        event.stopPropagation();

        decryptAllFields(document.getElementById('password'));
    });
});