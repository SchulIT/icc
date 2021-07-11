import { createApp } from 'vue'
import axios from 'axios'
import VueAxios from "vue-axios";
import Students from "./components/entry/Students";

const app = createApp({
    data() {
        return {
            isSubstitution: false
        }
    },
    components: {
        Students
    },
    methods: {

    }
})

app.use(VueAxios, axios);
app.mount('#app');

document.addEventListener('DOMContentLoaded', function() {
    let isSubstitutionElement = document.getElementById('entry_isSubstitution');

    let showOrHideSubjectAndTeacherField = function() {
        ['entry_subject', 'entry_teacher'].forEach(function(id) {
            let element = document.getElementById(id).closest('.form-group');
            if(isSubstitutionElement.checked) {
                element.classList.remove('d-none');
            } else {
                element.classList.add('d-none');
            }
        });
    };

    isSubstitutionElement.addEventListener('change', showOrHideSubjectAndTeacherField);
    showOrHideSubjectAndTeacherField(); // initial call
});