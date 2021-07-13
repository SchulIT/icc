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
