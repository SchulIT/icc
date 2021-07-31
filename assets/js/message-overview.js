import { createApp } from 'vue'
import axios from 'axios'
import VueAxios from "vue-axios";

import Translations from "./plugins/trans";
import BulkRemoveButton from "./components/messages/BulkRemoveButton";
import { Dropdown } from 'bootstrap.native';

const app = createApp({
    components: {
        BulkRemoveButton
    },
    methods: { },
    data() {
        return { };
    },
    mounted() {
        this.$el.querySelectorAll('[data-toggle=dropdown]').forEach(function(el) {
            new Dropdown(el);
        });
    }
});

app.use(Translations);
app.use(VueAxios, axios);
app.mount('#app');
