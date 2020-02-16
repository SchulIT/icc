require('../css/appointments.scss');

import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import listPlugin from '@fullcalendar/list'
import interactionPlugin from '@fullcalendar/interaction'
import bootstrapPlugin from '@fullcalendar/bootstrap';
import deLocale from '@fullcalendar/core/locales/de';

require('@fullcalendar/core/locales-all');

document.addEventListener('DOMContentLoaded', function () {
    var appEl = document.getElementById('appointments');
    var lastQuery = { };

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
        ]
    });

    calendar.render();

    var studentIdEl = document.getElementById('student');
    var gradeIdEl = document.getElementById('grade');
    var categoriyIdsEl = document.getElementById('categories');

    [studentIdEl, gradeIdEl, categoriyIdsEl ].forEach(function(el) {
        el.addEventListener('change', function(el) {
            loadEvents();
        });
    });

    function loadEvents() {
        var query = { };

        [studentIdEl, gradeIdEl, categoriyIdsEl ].forEach(function(el) {
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