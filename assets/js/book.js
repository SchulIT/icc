import { createApp } from 'vue'
import axios from 'axios'
import VueAxios from "vue-axios";

import Translations from "./plugins/trans";
import LessonCancelButton from "./components/book/LessonCancelButton";
import LessonCreateEntryButton from "./components/book/LessonCreateEntryButton";

const app = createApp({
    components: {
        LessonCancelButton,
        LessonCreateEntryButton
    },
    methods: { },
    data() {
        return { };
    }
});

app.use(Translations);
app.use(VueAxios, axios);
app.mount('#app');

document.addEventListener('DOMContentLoaded', function() {
/*
    document.querySelectorAll('[data-toggle=modal]').forEach(function(el) {
        el.addEventListener('click', function(event) {
            event.preventDefault();

            let targetSelector = el.getAttribute('data-target');
            let target = document.querySelector(targetSelector);

            if (target === null) {
                console.error("Target '" + targetSelector + '" was not found.');
                return;
            }

            let modal = new bsn.Modal(target);
            modal.show();
        })
    });

    document.querySelectorAll('[data-trigger=cancel-lesson]').forEach(function(el) {
        el.addEventListener('click', function(event) {
            let tuitionUuid = el.getAttribute('data-tuition');
            let lessonStart = el.getAttribute('data-lesson-start');
            let lessonEnd = el.getAttribute('data-lesson-end');
            let subject = el.getAttribute('data-subject');
            let teacher = el.getAttribute('data-teacher');
            let date = el.getAttribute('data-date');
            let rawDate = el.getAttribute('data-raw-date');

            let target = el.getAttribute('data-target');
            let targetModal = document.querySelector(target);

            targetModal.querySelector('#lesson_entry_cancel_lessonStart').value = lessonStart;
            targetModal.querySelector('#lesson_entry_cancel_lessonEnd').value = lessonEnd;
            targetModal.querySelector('input[name=tuition]').value = tuitionUuid;
            targetModal.querySelector('input[name=date]').value = rawDate;
            targetModal.querySelector('[data-id=subject]').innerHTML = subject;
            targetModal.querySelector('[data-id=teacher]').innerHTML = teacher;
            targetModal.querySelector('[data-id=date]').innerHTML = date;
        });
    });*/
});