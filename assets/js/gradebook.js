const crypto = require('easy-web-crypto');

let decryptedKey = null;

function decryptAll() {
    if(decryptedKey === null) {
        return;
    }

    document.querySelectorAll('[data-encrypted]').forEach(async function(element) {
        let input = element;
        let select = document.querySelector(element.getAttribute('data-select'));

        if(select === null) {
            return;
        }

        let encryptedValue = element.value;

        if(select.getAttribute('data-readonly') === undefined || select.getAttribute('data-readonly') !== 'true') {
            select.removeAttribute('disabled');
        }

        select.addEventListener('change', async function(element) {
            let encryptedValue = '';
            if(this.value !== '') {
                encryptedValue = JSON.stringify(await crypto.encrypt(decryptedKey, this.value));
            }

            input.value = encryptedValue;
        });

        if(encryptedValue === null || encryptedValue === '') {
            return;
        }

        select.value = await crypto.decrypt(decryptedKey, JSON.parse(encryptedValue));
    });
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
            decryptAll();
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
});