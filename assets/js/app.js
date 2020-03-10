require('../css/app.scss');

require('bootstrap.native');
require('emojione');
import Choices from "choices.js";

let bsCustomFileInput = require('bs-custom-file-input');
let ClipboardJS = require('clipboard');
var bsn = require('bootstrap.native');

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
            removeItemButton: removeItemButton,
            callbackOnCreateTemplates: function(template) {
                return {
                    item: (classNames, data) => {
                        return template(`
          <div class="${classNames.item} ${
                            data.highlighted
                                ? classNames.highlightedState
                                : classNames.itemSelectable
                        } ${
                            data.placeholder ? classNames.placeholder : ''
                        }" data-item data-id="${data.id}" data-value="${data.value}" ${
                            data.active ? 'aria-selected="true"' : ''
                        } ${data.disabled ? 'aria-disabled="true"' : ''}>
            ${data.customProperties != null && data.customProperties.startsWith('#') ? '<span class="color-rect" style="background: ' + data.customProperties + ';"></span>' : '' } ${data.label}
          </div>
        `);
                    },
                    choice: (classNames, data) => {
                        return template(`
          <div class="${classNames.item} ${classNames.itemChoice} ${
                            data.disabled ? classNames.itemDisabled : classNames.itemSelectable
                        }" data-select-text="${this.config.itemSelectText}" data-choice ${
                            data.disabled
                                ? 'data-choice-disabled aria-disabled="true"'
                                : 'data-choice-selectable'
                        } data-id="${data.id}" data-value="${data.value}" ${
                            data.groupId > 0 ? 'role="treeitem"' : 'role="option"'
                        }>
             ${data.customProperties != null && data.customProperties.startsWith('#') ? '<span class="color-rect" style="background: ' + data.customProperties + ';"></span>' : '' } ${data.label}
          </div>
        `);
                    },
                };
            },
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

    document.querySelectorAll('[title]').forEach(function(el) {
        new bsn.Tooltip(el, {
            placement: 'bottom'
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    let container = document.getElementById('menu');

    let openElement = function(element) {
        if(element === null) {
            return;
        }

        let targetSelector = element.getAttribute('data-menu');

        if(targetSelector === null) { // Element has no menu
            return;
        }

        let target = document.querySelector(targetSelector);

        if(target === null) { // Target element is not available -> open nothing!
            return;
        }

        // Close currently open (or active) element
        let activeOrOpen = container.querySelector('li.nav-item.open') || container.querySelector('li.nav-item.active') || container.querySelector('li.nav-item.current_ancestor');

        if(activeOrOpen !== null) {
            closeElement(activeOrOpen.querySelector('a[data-menu]'), false);
        }

        element.closest('.nav-item').classList.add('open');

        target.classList.remove('hide');
        target.classList.add('show');
    };

    let closeElement = function(element, openActiveElement) {
        if(openActiveElement === true) {
            let active = container.querySelector('li.nav-item.active') || container.querySelector('li.nav-item.current_ancestor');

            if(active !== null) {
                openElement(active.querySelector('a[data-menu]'));
            }
        }

        if(element === null) {
            return;
        }

        let targetSelector = element.getAttribute('data-menu');

        if(targetSelector === null) { // Element has no menu
            return;
        }

        let target = document.querySelector(targetSelector);

        if(target === null) { // Target element is not available -> open nothing!
            return;
        }

        element.closest('.nav-item').classList.remove('open');

        target.classList.remove('show');
        target.classList.add('hide');
    };

    document.addEventListener('click', function(event) {
        let clickedElement = event.target;

        // Case 1: clicked into submenu
        let submenuContainer = clickedElement.closest('#submenu');

        if(submenuContainer !== null) {
            // Clicked into the submenu container -> do nothing!
            return;
        }

        // Case 2: clicked into again -> close
        let menuContainer = clickedElement.closest('.nav-item');

        if(menuContainer !== null && menuContainer.classList.contains('open')) {
            closeElement(menuContainer.querySelector('a[data-menu]'), true)
        }

        // Other cases: clicked somewhere else -> close menu
        let currentlyOpenElement = container.querySelector('li.open');

        if(currentlyOpenElement !== null && currentlyOpenElement.classList.contains('active') === false && currentlyOpenElement.classList.contains('current_ancestor') === false) {
            closeElement(currentlyOpenElement.querySelector('a[data-menu]'), true);
        } else {
            // no element is open -> prevent the active menu from being closed
            closeElement(null, true);
        }
    });

    document.querySelectorAll('a[data-menu]').forEach(function(el) {
        el.addEventListener('click', function(event) {
            event.preventDefault();
            event.stopPropagation();

            openElement(el);
        });
    });
});