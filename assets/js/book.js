import { createApp } from 'vue'
import axios from 'axios'
import VueAxios from "vue-axios";

import Translations from "./plugins/trans";
import LessonCancelBulkButton from "./components/book/LessonCancelBulkButton";
import LessonAttendanceInfo from "./components/book/LessonAttendanceInfo";
import Entry from "./components/entry/Entry";
import SuppressButton from "./components/integrity_check/SuppressButton.vue";
import Attendance from "./components/student/Attendance";
import AttendanceOverview from "./components/student/AttendanceOverview.vue";
import Students from "./components/entry/Students";

const app = createApp({
    components: {
        Entry,
        LessonCancelBulkButton,
        LessonAttendanceInfo,
        Attendance,
        AttendanceOverview,
        SuppressButton,
        Students
    },
    methods: { },
    data() {
        return { };
    }
});

let axiosWithoutInterceptors = axios.create();
axiosWithoutInterceptors.interceptors.request.clear();

let lastAuthCheck = new Date().getTime();
let checkIntervalInMilliseconds = 1 * 60 * 1000;

axios.interceptors.request.use(
    async function (config) {
        let authenticated = await axiosWithoutInterceptors.get('/authenticated');
        if(authenticated.data.authenticated === true) {
            lastAuthCheck = new Date().getTime();
            return config;
        }

        location.reload();
    },
    function (error) {
        return Promise.reject(error);
    },
    {
        synchronous: false,
        runWhen: () => lastAuthCheck < (new Date().getTime() - checkIntervalInMilliseconds)
    }
);

app.use(Translations);
app.use(VueAxios, axios);
app.mount('#app');