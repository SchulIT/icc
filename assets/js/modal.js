import {Modal} from "bootstrap";

document.addEventListener('DOMContentLoaded', function(event) {
    document.querySelectorAll('[data-toggle=modal]').forEach(function(el) {
        el.addEventListener('click', function(event) {
            event.preventDefault();

            let targetSelector = el.getAttribute('data-target');
            let target = document.querySelector(targetSelector);

            if (target === null) {
                console.error("Target '" + targetSelector + '" was not found.');
                return;
            }

            if(el.hasAttribute('data-modal-content')) {
                let contentContainer = target.querySelector('.modal-body');
                contentContainer.innerHTML = el.getAttribute('data-modal-content');
            }

            let formSelector = el.getAttribute('data-entity-target');
            let formValue = el.getAttribute('data-entity-id');
            if(formSelector !== null) {
                let form = target.querySelector(formSelector);

                if(form === null) {
                    console.error("Entity target '" + formSelector + '" was not found.');
                    return;
                }

                form.value = formValue;
            }

            let modal = new Modal(target);
            modal.show();
        });
    });
});