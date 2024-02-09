import { Modal } from "bootstrap";

document.addEventListener('DOMContentLoaded', function() {
    for(let link of document.querySelectorAll('[data-modal-form]')) {
        link.addEventListener('click', function(event) {
            event.preventDefault();

            let modalEl = document.querySelector(link.getAttribute('data-modal-form'));
            let modal = Modal.getOrCreateInstance(modalEl);

            modalEl.querySelector('.time').innerHTML = link.getAttribute('data-time');
            modalEl.querySelector('.teacher').innerHTML = link.getAttribute('data-teacher');

            modalEl.querySelector('form').action = link.href;

            modal.show();
        });
    }
});