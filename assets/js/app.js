require('../css/app.scss');

require('bootstrap.native');
require('emojione');
import Choices from "choices.js";

let bsCustomFileInput = require('bs-custom-file-input');
let ClipboardJS = require('clipboard');
var bsn = require('bootstrap.native');

require('../../vendor/schoolit/common-bundle/Resources/assets/js/polyfill');
require('../../vendor/schoolit/common-bundle/Resources/assets/js/menu');

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
            let confirmModalSelector = el.getAttribute('data-confirm');
            let form = this.closest('form');

            if(confirmModalSelector === null || confirmModalSelector === '') {
                form.submit();
                return;
            }

            let modalEl = document.querySelector(confirmModalSelector);
            let modal = new bsn.Modal(modalEl);
            modal.show();

            let confirmBtn = modalEl.querySelector('.confirm');
            confirmBtn.addEventListener('click', function(event) {
                event.preventDefault();
                event.stopImmediatePropagation();

                console.log(form);

                form.submit();
            });
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

    document.querySelectorAll('[data-toggle=popover]').forEach(function(el) {
        let title = el.getAttribute('data-popover-title');

        let contentSelector = el.getAttribute('data-popover-container');
        let content = document.querySelector(contentSelector).innerHTML;

        var template = '<div class="popover" role="tooltip">' +
            '<div class="arrow"></div>' +
            '<h3 class="popover-header">' + title + '</h3>' +
            '<div class="popover-body">' +
             content +
            '</div></div>';

        new bsn.Popover(el, {
            placement: 'bottom',
            template: template,
            trigger: 'hover',
            animation: 'none'
        });
    });

    let arrowDown = 'fa-chevron-down';
    let arrowUp = 'fa-chevron-up';

    document.querySelectorAll('[data-toggle=table-collapse]').forEach(function(el) {
        el.addEventListener('click', function(event) {
            event.preventDefault();

            let targetSelector = el.getAttribute('data-target');
            let targets = document.querySelectorAll(targetSelector);

            let indicator = el.querySelector('.indicator');

            if(indicator === null) {
                targets.forEach(function (target) {
                    if (target.classList.contains('collapse')) {
                        target.classList.remove('collapse');
                    } else {
                        target.classList.add('collapse');
                    }
                });
            } else {
                if(indicator.classList.contains(arrowDown)) { // show
                    indicator.classList.remove(arrowDown);
                    indicator.classList.add(arrowUp);
                    targets.forEach(function(target) {
                        target.classList.remove('collapse');
                    });
                } else { // hide
                    indicator.classList.remove(arrowUp);
                    indicator.classList.add(arrowDown);
                    targets.forEach(function(target) {
                        target.classList.add('collapse');
                    });
                }
            }
        });
    });
});

