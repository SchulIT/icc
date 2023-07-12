import { createApp } from 'vue'
import axios from 'axios'
import VueAxios from "vue-axios";

import Translations from "./plugins/trans";
import BulkRemoveButton from "./components/messages/BulkRemoveButton";

const app = createApp({
    components: {
        BulkRemoveButton
    },
    methods: { },
    data() {
        return { };
    }
});

app.use(Translations);
app.use(VueAxios, axios);
app.mount('#app');
