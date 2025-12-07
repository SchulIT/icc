require('../css/app.scss');

import Choices from "choices.js";
import { v4 as uuidv4 } from 'uuid';
import { DataTable } from "simple-datatables";
import { Modal, Tooltip, Popover } from "bootstrap";

let ClipboardJS = require('clipboard');

require('../../vendor/schulit/common-bundle/assets/js/polyfill');
require('../../vendor/schulit/common-bundle/assets/js/menu');
require('../../vendor/schulit/common-bundle/assets/js/icon-picker');
require('../../vendor/schulit/common-bundle/assets/js/dropdown-polyfill');

document.addEventListener('DOMContentLoaded', function() {

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

    document.querySelectorAll('table[data-table]').forEach(function(el) {
        el.datatable = new DataTable(el, {
            searchable: false,
            fixedHeight: false,
            paging: false,
            truncatePager: false
        });
    });

    document.querySelectorAll('a[data-trigger=scroll]').forEach(function(el) {
        el.addEventListener('click', function(event) {
            event.preventDefault();

            try {
                let target = document.querySelector(el.getAttribute('href'));
                if (target !== null) {
                    target.scrollIntoView({behavior: 'smooth'});
                }
            } catch {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
        });
    });

    document.querySelectorAll('[data-trigger="submit"]').forEach(function (el) {
        let eventName = 'change';

        if(el.nodeName === 'BUTTON') {
            eventName = 'click';
        }

        el.addEventListener(eventName, function (event) {
            let confirmModalSelector = el.getAttribute('data-confirm');
            let form = this.closest('form');

            if(confirmModalSelector === null || confirmModalSelector === '') {
                form.submit();
                return;
            }

            let modalEl = document.querySelector(confirmModalSelector);
            let modal = new Modal(modalEl);
            modal.show();

            let confirmBtn = modalEl.querySelector('.confirm');
            confirmBtn.addEventListener('click', function(event) {
                event.preventDefault();
                event.stopImmediatePropagation();

                form.submit();
            });
        });
    });

    let initializeChoice = function(el, selected) {
        let removeItemButton = false;

        if(el.getAttribute('multiple') !== null) {
            removeItemButton = true;
        }

        el.choices = new Choices(el, {
            itemSelectText: '',
            shouldSort: false,
            shouldSortItems: false,
            removeItemButton: removeItemButton,
            searchResultLimit: 10,
            searchFields: ['label']
        });

        if(selected !== undefined) {
            if(Array.isArray(selected) === false) {
                for(let i = 0; i < selected.length; i++) {
                    el.choices.setChoiceByValue(selected[i]);
                }
            }

            el.choices.setChoiceByValue(selected);
        }
    };

    document.querySelectorAll('select[data-choice=true]').forEach(el => initializeChoice(el));

    document.querySelectorAll('[data-toggle=multiple-choice]').forEach(function(el) {
        el.addEventListener('change', function(event) {
            let target = document.querySelector(el.getAttribute('data-target'));

            if(el.checked) {
                target.setAttribute("multiple", "multiple");
            } else {
                target.removeAttribute('multiple');
            }

            let selected = target.choices.getValue(true);
            target.choices.destroy();
            initializeChoice(target, selected);
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

            if('choices' in target) {
                values.forEach(function (value) {
                    target.choices.setChoiceByValue(value);
                });
            } else {
                target.querySelectorAll('option').forEach(function (optionEl) {
                    if (values.indexOf(optionEl.value) > -1) {
                        optionEl.selected = true;
                    }
                });
            }
        });
    });

    document.querySelectorAll('[data-toggle=unselect]').forEach(function(el) {
        el.addEventListener('click', function(event) {
            event.preventDefault();

            let targetSelector = this.getAttribute('data-target');
            let target = document.querySelector(targetSelector);

            if('choices' in target) {
                target.choices.removeActiveItems();
            } else {
                target.querySelectorAll('option').forEach(function(el) { el.selected = false; });
            }
        });
    });

    document.querySelectorAll('[title]').forEach(function(el) {
        new Tooltip(el, {
            placement: 'bottom'
        });
    });

    document.querySelectorAll('[data-toggle=xhr-popover]').forEach(function(el) {
        let title = el.getAttribute('data-popover-title');
        let url = el.getAttribute('data-popover-url');
        let contentId = 'popover-' + uuidv4();
        let spinner = '<i class="fas fa-spinner fa-pulse"></i>';
        let initialized = false;

        let popover = new Popover(el, {
            placement: 'bottom',
            title: title,
            content: spinner,
            html: true,
            trigger: 'hover'
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

                    popover.setContent({
                        '.popover-body': xhr.responseText
                    });
                    initialized = true;
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

        new Popover(el, {
            placement: 'bottom',
            template: template,
            trigger: 'hover',
            animation: 'none'
        });
    });

    let arrowDown = 'fa-chevron-down';
    let arrowUp = 'fa-chevron-up';

    let collapse = function(el) {
        changeState(el, true);
    };

    let show = function(el) {
        changeState(el, false);
    };

    let isCollapsed = function(el) {
        let targetSelector = el.getAttribute('data-target');
        let targets = document.querySelectorAll(targetSelector);

        let indicator = el.querySelector('.indicator');

        if(indicator === null) {
            let collapsed = true;

            targets.forEach(function(target) {
                if(target.classList.contains('collapse') !== true) {
                    collapsed = false;
                }
            });
        } else {
            return indicator.classList.contains(arrowDown);
        }
    };

    let changeState = function(el, collapsed) {
        let targetSelector = el.getAttribute('data-target');
        let targets = document.querySelectorAll(targetSelector);

        let indicator = el.querySelector('.indicator');

        el.setAttribute('data-is-collapsed', collapsed);

        if(indicator === null) {
            targets.forEach(function (target) {
                if (collapsed === false) {
                    target.classList.remove('collapse');
                } else {
                    target.classList.add('collapse');
                }
            });
        } else {
            if(collapsed === false) { // show
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
    };

    let toggle = function(el) {
        changeState(el, !isCollapsed(el));
    };

    document.querySelectorAll('[data-toggle=table-collapse]').forEach(function(el) {
        el.addEventListener('click', function(event) {
            event.preventDefault();
            toggle(el);
        });
    });

    document.querySelectorAll('[data-toggle=table-collapse-all]').forEach(function(el) {
        el.addEventListener('click', function(event) {
            event.preventDefault();

            let containerSelector = el.getAttribute('data-container');
            let container = document.querySelectorAll(containerSelector);

            container.forEach(function(containerEl) {
                containerEl.querySelectorAll('[data-toggle=table-collapse]').forEach(function(collapseEl) {
                    collapse(collapseEl);
                });
            });
        });
    });

    document.querySelectorAll('[data-toggle=table-show-all]').forEach(function(el) {
        el.addEventListener('click', function(event) {
            event.preventDefault();

            let containerSelector = el.getAttribute('data-container');
            let container = document.querySelectorAll(containerSelector);

            container.forEach(function(containerEl) {
                containerEl.querySelectorAll('[data-toggle=table-collapse]').forEach(function(collapseEl) {
                    show(collapseEl);
                });
            });
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

            for(let leftIdx = startIdx-1, rightIdx = startIdx + 1; leftIdx >= 0 || rightIdx < element.children.length; leftIdx--, rightIdx++) {
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

    document.querySelectorAll('[data-apply-studygroup-target]').forEach(function(element) {
        element.closest('.apply-studygroup-container').querySelector('button').addEventListener('click', function(e) {
            let choicesElId = element.getAttribute('data-apply-studygroup-target');
            let choicesEl = document.querySelector(choicesElId);

            if(choicesEl === null) {
                console.error('Did not find choice with id ' + choicesElId);
                return;
            }

            element.value.split(',').forEach(function(value) {
                choicesEl.choices.setChoiceByValue("" + value);
            });
        });
    });

    document.querySelectorAll('input[data-enter]').forEach(function(element) {
        let targetEl = document.querySelector(element.getAttribute('data-enter'));

        if(targetEl === null) {
            console.error('Element was not found');
            return;
        }

        element.addEventListener('keydown', function(event) {
            if(event.key === 'Enter') {
                targetEl.click();
            }
        });
    });

    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', event => {
            let button = event.submitter;

            if(button === null) {
                return;
            }

            if(!button.classList.contains('btn')) {
                return;
            }



            let icon = button.querySelector('i');

            if(icon === null) {
                icon = document.createElement('i');
                icon.className = 'fa-solid fa-spinner fa-spin';
                button.innerHTML = icon.outerHTML + button.innerHTML;
            } else {
                icon.className = 'fa-solid fa-spinner fa-spin';
            }

            if(button.getAttribute('value') !== null) {
                button.classList.add('disabled');
            } else {
                button.setAttribute('disabled', 'disabled');
            }
        });
    });
});

