import Translator from "bazinga-translator";
const de = require('../translations/de.json');
Translator.fromJSON(de);

export default {
    install: (app, options) => {
        app.config.globalProperties.$trans = (key, parameters = {}, domain = "messages", locale = "de") => {
            return Translator.trans(key, parameters, domain, locale);
        };

        app.config.globalProperties.$transChoice= (id, number, parameters, domain, locale) => {
            return Translator.transChoice(id, number, parameters, domain, locale);
        };

        app.provide('translator', Translator);
    }
}