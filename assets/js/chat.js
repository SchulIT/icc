import { Modal, Tooltip, Popover } from "bootstrap";

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('button[data-edit]').forEach(function(el) {
        el.addEventListener('click', function(event) {
            event.preventDefault();
            let messageId = this.getAttribute('data-edit');

            document.querySelector('.content-' + messageId).classList.add('hide');
            document.querySelector('.remove-' + messageId).classList.add('hide');
            document.querySelector('.edit-' + messageId).classList.remove('hide');

            let textarea = document.querySelector('.edit-' + messageId + ' textarea');
            textarea.editor.codemirror.focus();
            textarea.editor.codemirror.setCursor(textarea.editor.codemirror.lineCount(), 0);
        });
    });

    document.querySelectorAll('button[data-remove]').forEach(function(el) {
        el.addEventListener('click', function(event) {
            event.preventDefault();
            let messageId = this.getAttribute('data-remove');

            document.querySelector('.content-' + messageId).classList.add('hide');
            document.querySelector('.remove-' + messageId).classList.remove('hide');
            document.querySelector('.edit-' + messageId).classList.add('hide');
        });
    });

    document.querySelectorAll('button[data-cancel]').forEach(function(el) {
        el.addEventListener('click', function(event) {
            event.preventDefault();
            let messageId = this.getAttribute('data-cancel');

            document.querySelector('.content-' + messageId).classList.remove('hide');
            document.querySelector('.remove-' + messageId).classList.add('hide');
            document.querySelector('.edit-' + messageId).classList.add('hide');
        });
    });

    document.querySelector('button[data-rename]')?.addEventListener('click', function(event) {
        event.preventDefault();

        document.querySelector('.rename-chat').classList.remove('hide');
        document.querySelector('.chat-topic').classList.add('hide');
    });

    document.querySelector('button[data-cancel-rename]')?.addEventListener('click', function(event) {
        event.preventDefault();

        document.querySelector('.rename-chat').classList.add('hide');
        document.querySelector('.chat-topic').classList.remove('hide');
    });
});