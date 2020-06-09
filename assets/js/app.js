require('../css/app.scss');

require('emojione');
import Choices from "choices.js";
import { v4 as uuidv4 } from 'uuid';

let bsCustomFileInput = require('bs-custom-file-input');
let ClipboardJS = require('clipboard');
let bsn = require('bootstrap.native');

require('../../vendor/schoolit/common-bundle/Resources/assets/js/polyfill');
require('../../vendor/schoolit/common-bundle/Resources/assets/js/menu');

require('./webpush');

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

    document.querySelectorAll('[title]').forEach(function(el) {
        new bsn.Tooltip(el, {
            placement: 'bottom'
        });
    });

    document.querySelectorAll('[data-toggle=xhr-popover]').forEach(function(el) {
        let title = el.getAttribute('data-popover-title');
        let url = el.getAttribute('data-popover-url');
        let contentId = 'popover-' + uuidv4();
        let spinner = '<i class="fas fa-spinner fa-pulse"></i>';
        let initialized = false;

        let template = '<div class="popover" role="tooltip">' +
            '<div class="arrow"></div>' +
            '<h3 class="popover-header">' + title + '</h3>' +
            '<div class="popover-body" id="' + contentId +'">' +
            spinner +
            '</div></div>';

        new bsn.Popover(el, {
            placement: 'bottom',
            template: template,
            trigger: 'hover',
            animation: 'none'
        });

        el.addEventListener('shown.bs.popover', function(event) {
            if(initialized === true) {
                return;
            }

            let xhr = new XMLHttpRequest();
            xhr.onload = function() {
                if(xhr.status >= 200 && xhr.status < 300) {
                    // Update popover
                    let contentElement = document.getElementById(contentId);

                    if(contentElement !== null) {
                        contentElement.innerHTML = xhr.responseText;
                    }

                    el.Popover.template = '<div class="popover" role="tooltip">' +
                        '<div class="arrow"></div>' +
                        '<h3 class="popover-header">' + title + '</h3>' +
                        '<div class="popover-body" id="' + contentId +'">' +
                            xhr.responseText +
                        '</div></div>';

                    /*new bsn.Popover(el, {
                        placement: 'bottom',
                        template: template,
                        trigger: 'hover',
                        animation: 'none'
                    });*/

                    initialized = true;
                    console.log(el.Popover);
                } else {
                    console.error('XMLHttpRequest error');
                    console.error(xhr);
                }
            }
            xhr.open('GET', url);
            xhr.send();
        });
    });

    document.querySelectorAll('[data-toggle=popover]').forEach(function(el) {
        let title = el.getAttribute('data-popover-title');

        let contentSelector = el.getAttribute('data-popover-container');
        let content = document.querySelector(contentSelector).innerHTML;

        let template = '<div class="popover" role="tooltip">' +
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

    let hideElementsIfNotEnoughSpace = function() {
        document.querySelectorAll('[data-trigger=resize-hide]').forEach(function(element) {
            // Important: retrieve width at the beginning because after applying the hide class, the element has 0 width
            let children = Array.prototype.slice.call(element.children);

            children.forEach(function(child) {
                if(child.hasAttribute('data-width') === false) {
                    child.setAttribute('data-width', child.offsetWidth);
                }
            });

            let containerWidth = element.clientWidth;
            let occupiedWidth = 0;
            let canAddChild = true;

            console.log('container: ' + containerWidth);

            // First: add all children which are prevented from begin hidden
            element.querySelectorAll('[data-resize=show]').forEach(function (child) {
                let childWidth = parseInt(child.getAttribute('data-width'));
                occupiedWidth += childWidth;
                child.classList.remove('hide');
            });

            // Second: start with the item which is prevented to be hidden and go left and right starting from there
            let startIdx = 0;
            for(let idx = 0; idx < element.children.length; idx++) {
                let elementAtIdx = element.children[idx];

                if(elementAtIdx === null || elementAtIdx.hasAttribute('data-resize') === false || elementAtIdx.getAttribute('data-resize') === 'show' || elementAtIdx.getAttribute('data-resize') !== 'prevent') {
                    continue;
                }

                startIdx = idx;
            }

            let startElement = element.children[startIdx] || null;
            if(startElement !== null) {
                occupiedWidth += parseInt(startElement.getAttribute('data-width'));
                startElement.classList.remove('hide');
            }

            console.log('length: ' + element.children.length + '/startIdx:' + startIdx);
            for(let leftIdx = startIdx-1, rightIdx = startIdx + 1; leftIdx >= 0 || rightIdx < element.children.length; leftIdx--, rightIdx++) {
                console.log('left: ' + leftIdx + '/right: ' + rightIdx);
                let leftElement = element.children[leftIdx] || null;
                if(leftElement !== null && leftElement.hasAttribute('data-width') && leftElement.getAttribute('data-resize') !== 'show') {
                    let leftWidth = parseInt(leftElement.getAttribute('data-width'));
                    if(canAddChild && occupiedWidth + leftWidth < containerWidth) {
                        occupiedWidth += leftWidth;
                        leftElement.classList.remove('hide');
                    } else {
                        canAddChild = false;
                        leftElement.classList.add('hide');
                    }
                }

                let rightElement = element.children[rightIdx] || null;
                if(rightElement !== null && rightElement.hasAttribute('data-width')  && rightElement.getAttribute('data-resize') !== 'show') {
                    let rightWidth = parseInt(rightElement.getAttribute('data-width'));
                    if(canAddChild && occupiedWidth + rightWidth < containerWidth) {
                        occupiedWidth += rightWidth;
                        rightElement.classList.remove('hide');
                    } else {
                        canAddChild = false;
                        rightElement.classList.add('hide');
                    }
                }
            }
        });
    };

    window.addEventListener('resize', function(event) {
        hideElementsIfNotEnoughSpace();
    });

    hideElementsIfNotEnoughSpace();
});

