require('../css/appointments.scss');

import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import listPlugin from '@fullcalendar/list'
import interactionPlugin from '@fullcalendar/interaction'
import bootstrapPlugin from '@fullcalendar/bootstrap5';
import deLocale from '@fullcalendar/core/locales/de';
import Choices from "choices.js";
import Popover from "bootstrap/js/dist/popover";

require('@fullcalendar/core/locales-all');

document.addEventListener('DOMContentLoaded', function () {
    let options = {
        itemSelectText: '',
        shouldSort: false,
        shouldSortItems: false,
        removeItemButton: true
    };

    let sectionChoice = document.getElementById('section') !== null ? new Choices(document.getElementById('section'), { }) : null;
    let studentChoice = document.getElementById('student') !== null ? new Choices(document.getElementById('student'), options) : null;
    let studyGroupChoice = document.getElementById('study_group') !== null ? new Choices(document.getElementById('study_group'), options) : null;
    let teacherChoice = document.getElementById('teacher') !== null ? new Choices(document.getElementById('teacher'), options) : null;
    let categoriesChoice = document.getElementById('categories') !== null ? new Choices(document.getElementById('categories'), options) : null;
    let examGradesChoice = document.getElementById('exam_grades') !== null ? new Choices(document.getElementById('exam_grades'), options) : null;

    let suppressFilterChangedEvent = false;
    let appEl = document.getElementById('appointments');
    let lastQuery = { };
    let popovers = { };

    let calendarEl = document.getElementById('calendar');
    let calendar = new Calendar(calendarEl, {
        plugins: [
            dayGridPlugin,
            timeGridPlugin,
            listPlugin,
            interactionPlugin,
            bootstrapPlugin
        ],
        headerToolbar: {
            start: 'prev,next today',
            center: 'title',
            end: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        buttonIcons: {
            prev: ' fa fa-chevron-left',
            next: ' fa fa-chevron-right'
        },
        weekNumbers: true,
        themeSystem: 'bootstrap5',
        locales: [ deLocale ],
        locale: 'de',
        eventSources: [
            {
                id: 'json',
                url: appEl.getAttribute('data-url'),
                method: 'GET',
                extraParams: function() {
                    return lastQuery;
                }
            }
        ],
        eventMouseEnter: function(info) {
            let event = info.event;
            let title = event.title;
            let content = event.extendedProps.content;
            let view = event.extendedProps.view;
            let confirmation_status = event.extendedProps.confirmation_status;

            let titleHtml = title + ' <span class="badge" style="background: ' + event.backgroundColor + '; color: ' + event.textColor + ';">' + event.extendedProps.category + '</span>';
            let contentHtml = '';

            console.log(titleHtml);

            if(confirmation_status !== null) {
                contentHtml += '<span class="badge text-bg-danger"><i class="fa fa-information-circle"></i> ' + confirmation_status + '</span>';
            }

            if(content !== null) {
                contentHtml += '<p>' + content + '</p>';
            }

            view.forEach(function(viewItem) {
                contentHtml += '<p><span class="text-muted">' + viewItem.label + '</span> ' + viewItem.content + '</p>';
            });

            let popover = new Popover(info.el, {
                placement: 'right',
                trigger: 'manual',
                dismissible: true,
                animation: false,
                html: true,
                container: 'body',
                title: titleHtml,
                content: contentHtml,
                sanitize: false
            });
            popover.show();

            let eventId = event.id;
            popovers[eventId] = popover;
        },
        eventMouseLeave: function(info) {
            let eventId = info.event.id;
            if(popovers[eventId] !== null) {
                popovers[eventId].hide();
            }
        },
        loading: function(isLoading) {
            [studentChoice, studyGroupChoice, teacherChoice, categoriesChoice, examGradesChoice ].forEach(function(choices) {
                if(choices === null) {
                    return;
                }

                if(isLoading) {
                    choices.disable();
                } else {
                    choices.enable();
                }
            });
        }
    });

    [ studentChoice, studyGroupChoice, teacherChoice, categoriesChoice, examGradesChoice ].forEach(function(choices) {
        if(choices === null) {
            return;
        }

        choices.passedElement.element.addEventListener('change', function(el) {
            if(suppressFilterChangedEvent === false) {
                loadEvents(choices);
            }
        });
    });

    function loadEvents(initiator) {
        let query = {
            section: sectionChoice.getValue(true)
        };

        // Ensure that filters are not combined
        suppressFilterChangedEvent = true; // suppress any other changed events

        if(initiator === studentChoice) {
            studyGroupChoice?.setChoiceByValue('');
            teacherChoice?.setChoiceByValue('');
        } else if(initiator === studyGroupChoice) {
            studentChoice?.setChoiceByValue('');
            teacherChoice?.setChoiceByValue('');
        } else if(initiator === teacherChoice) {
            studentChoice?.setChoiceByValue('');
            studyGroupChoice?.setChoiceByValue('');
        }

        suppressFilterChangedEvent = false;

        // Serialize the filter data
        [studentChoice, studyGroupChoice, teacherChoice, categoriesChoice, examGradesChoice ].forEach(function(el) {
            if(el === null) {
                return;
            }

            query[el.passedElement.element.name] = el.getValue(true);
        });

        let eventSource = calendar.getEventSourceById('json');
        lastQuery = query;
        eventSource.refetch();
    }

    calendar.render();

    loadEvents(null);
});