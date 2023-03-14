import { createApp } from 'vue'
import axios from 'axios'
import VueAxios from "vue-axios";

import Translations from "./plugins/trans";
import LessonCancelButton from "./components/book/LessonCancelButton";
import LessonCreateEntryButton from "./components/book/LessonCreateEntryButton";
import LessonCancelBulkButton from "./components/book/LessonCancelBulkButton";
import LessonAttendanceInfo from "./components/book/LessonAttendanceInfo";
import Entry from "./components/entry/Entry";
import Attendance from "./components/student/Attendance";
import AttendanceOverview from "./components/student/AttendanceOverview.vue";

const app = createApp({
    components: {
        Entry,
        LessonCancelButton,
        LessonCreateEntryButton,
        LessonCancelBulkButton,
        LessonAttendanceInfo,
        Attendance,
        AttendanceOverview
    },
    methods: { },
    data() {
        return { };
    }
});

app.use(Translations);
app.use(VueAxios, axios);
app.mount('#app');