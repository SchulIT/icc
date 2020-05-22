require('../css/appointments.scss');

import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import listPlugin from '@fullcalendar/list'
import interactionPlugin from '@fullcalendar/interaction'
import bootstrapPlugin from '@fullcalendar/bootstrap';
import deLocale from '@fullcalendar/core/locales/de';
let bsn = require('bootstrap.native');

require('@fullcalendar/core/locales-all');

document.addEventListener('DOMContentLoaded', function () {
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
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        themeSystem: 'bootstrap',
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

            let template = '<div class="popover" role="tooltip">' +
                '<div class="arrow"></div>' +
                '<h3 class="popover-header">' + title + ' <span class="badge" style="background: ' + event.backgroundColor + '; color: ' + event.textColor + '">' + event.extendedProps.category + '</span></h3>' +
                '<div class="popover-body">';

            if(content !== null) {
                template += '<p>' + content + '</p>';
            }

            view.forEach(function(viewItem) {
                template += '<p><span class="text-muted">' + viewItem.label + '</span> ' + viewItem.content + '</p>';
            });

            template += '</div></div>';

            let popover = new bsn.Popover(info.el, {
                placement: 'right',
                template: template,
                trigger: 'focus',
                dismissible: true,
                animation: 'none',
                delay: 1 // 0 is not sufficient enough, probably bsn thinks, that delay is not set if set to 0?!
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
        }
    });

    calendar.render();

    let studentIdEl = document.getElementById('student');
    let studyGroupIdEl = document.getElementById('study_group');
    let teacherIdEl = document.getElementById('teacher');
    let categoryIdsEl = document.getElementById('categories');
    let examGradeIdsEl = document.getElementById('exam_grades');

    [studentIdEl, studyGroupIdEl, teacherIdEl, categoryIdsEl, examGradeIdsEl ].forEach(function(el) {
        el.addEventListener('change', function(el) {
            loadEvents();
        });
    });

    function loadEvents() {
        let query = { };

        [studentIdEl, studyGroupIdEl, teacherIdEl, categoryIdsEl, examGradeIdsEl ].forEach(function(el) {
            if(el.multiple !== null && el.multiple !== false) {
                query[el.name] = Array.from(el.selectedOptions).map(x => x.value);
            } else {
                query[el.name] = el.value === "" ? null : el.value;
            }
        });

        let eventSource = calendar.getEventSourceById('json');
        lastQuery = query;
        eventSource.refetch();
    }

    loadEvents();
});