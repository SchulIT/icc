require('../css/app.scss');

require('bootstrap.native');
require('emojione');
import Choices from "choices.js";

let bsCustomFileInput = require('bs-custom-file-input');

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

    document.querySelectorAll('[data-trigger="submit"]').forEach(function (el) {
        el.addEventListener('change', function (event) {
            this.closest('form').submit();
        });
    });

    document.querySelectorAll('select[data-choice=true]').forEach(function(el) {
        console.log(el);

        new Choices(el, {
            itemSelectText: '',
            shouldSort: false,
            shouldSortItems: false
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
});