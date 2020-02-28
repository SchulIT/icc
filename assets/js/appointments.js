require('../css/appointments.scss');

import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import listPlugin from '@fullcalendar/list'
import interactionPlugin from '@fullcalendar/interaction'
import bootstrapPlugin from '@fullcalendar/bootstrap';
import deLocale from '@fullcalendar/core/locales/de';
var bsn = require('bootstrap.native');

require('@fullcalendar/core/locales-all');

document.addEventListener('DOMContentLoaded', function () {
    var appEl = document.getElementById('appointments');
    var lastQuery = { };
    var popovers = { };

    var calendarEl = document.getElementById('calendar');
    var calendar = new Calendar(calendarEl, {
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
            var event = info.event;
            var title = event.title;
            var content = event.extendedProps.content;
            var view = event.extendedProps.view;

            var template = '<div class="popover" role="tooltip">' +
                '<div class="arrow"></div>' +
                '<h3 class="popover-header">' + title + ' <span class="badge" style="background: ' + event.backgroundColor + '; color: ' + event.textColor + '">' + event.extendedProps.category + '</span></h3>' +
                '<div class="popover-body">' +
                '<p>' + content + '</p>';

            view.forEach(function(viewItem) {
                template += '<p><span class="text-muted">' + viewItem.label + '</span> ' + viewItem.content + '</p>';
            });

            template += '</div></div>';

            var popover = new bsn.Popover(info.el, {
                placement: 'right',
                template: template,
                trigger: 'focus',
                dismissible: true,
                animation: 'none',
                delay: 1 // 0 is not sufficient enough, probably bsn thinks, that delay is not set if set to 0?!
            });
            popover.show();

            var eventId = event.id;
            popovers[eventId] = popover;
        },
        eventMouseLeave: function(info) {
            var eventId = info.event.id;
            if(popovers[eventId] !== null) {
                popovers[eventId].hide();
            }
        }
    });

    calendar.render();

    var studentIdEl = document.getElementById('student');
    var gradeIdEl = document.getElementById('grade');
    var teacherIdEl = document.getElementById('teacher');
    var categoriyIdsEl = document.getElementById('categories');

    [studentIdEl, gradeIdEl, teacherIdEl, categoriyIdsEl ].forEach(function(el) {
        el.addEventListener('change', function(el) {
            loadEvents();
        });
    });

    function loadEvents() {
        var query = { };

        [studentIdEl, gradeIdEl, teacherIdEl, categoriyIdsEl ].forEach(function(el) {
            if(el.multiple !== null && el.multiple !== false) {
                query[el.name] = Array.from(el.selectedOptions).map(x => x.value);
            } else {

                query[el.name] = el.value === "" ? null : el.value;
            }
        });

        var eventSource = calendar.getEventSourceById('json');
        lastQuery = query;
        eventSource.refetch();
    }

    loadEvents();
});