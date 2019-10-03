require('../css/app.scss');

require('bootstrap.native');
require('emojione');
import Choices from "choices.js";

let bsCustomFileInput = require('bs-custom-file-input');
let ClipboardJS = require('clipboard');

/*
 * Polyfill for closest function (thanks, Mozilla! https://developer.mozilla.org/en-US/docs/Web/API/Element/closest#Polyfill)
 */
if (!Element.prototype.matches) {
    Element.prototype.matches = Element.prototype.msMatchesSelector || Element.prototype.webkitMatchesSelector;
}

if (!Element.prototype.closest) {
    Element.prototype.closest = function(s) {
        let el = this;

        do {
            if (el.matches(s)) return el;
            el = el.parentElement || el.parentNode;
        } while (el !== null && el.nodeType === 1);
        return null;
    };
}

document.addEventListener('DOMContentLoaded', function() {
    bsCustomFileInput.init();

    var clipboard = new ClipboardJS('[data-clipboard-text]');
    clipboard.on('success', function(e) {
        let node = e.trigger;
        let icon = node.querySelector('i.fa');

        if(icon !== null) {
            icon.classList.remove('fa-copy');
            icon.classList.add('fa-check');

            setInterval(function () {
                icon.classList.remove('fa-check');
                icon.classList.add('fa-copy');
            }, 5000);
        }
    });

    document.querySelectorAll('[data-trigger="submit"]').forEach(function (el) {
        el.addEventListener('change', function (event) {
            this.closest('form').submit();
        });
    });

    document.querySelectorAll('select[data-choice=true]').forEach(function(el) {
        let removeItemButton = false;

        if(el.getAttribute('multiple') !== null) {
            removeItemButton = true;
        }

        new Choices(el, {
            itemSelectText: '',
            shouldSort: false,
            shouldSortItems: false,
            removeItemButton: removeItemButton
        });
    });

    document.querySelectorAll('a[data-show]').forEach(function(el) {
        el.addEventListener('click', function(event) {
            event.preventDefault();

            let targetSelector = this.getAttribute('data-show');
            let target = document.querySelector(targetSelector);

            target.style.display = '';
        });
    });

    document.querySelectorAll('a[data-remove]').forEach(function(el) {
        el.addEventListener('click', function(event) {
            event.preventDefault();

            let targetSelector = this.getAttribute('data-remove');
            let target = document.querySelector(targetSelector);

            target.remove();
        });
    });

    document.querySelectorAll('[data-toggle=select-values]').forEach(function(el) {
        el.addEventListener('click', function(event) {
            event.preventDefault();

            let targetSelector = this.getAttribute('data-target');
            let target = document.querySelector(targetSelector);

            let values = this.getAttribute('data-select-values').split(',');

            target.querySelectorAll('option').forEach(function(optionEl) {
                if(values.indexOf(optionEl.value) > -1) {
                    optionEl.selected = true;
                }
            });
        });
    });
});
