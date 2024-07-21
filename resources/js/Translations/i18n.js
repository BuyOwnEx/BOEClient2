import { createVueI18nAdapter } from 'vuetify/locale/adapters/vue-i18n';
import { createI18n, useI18n } from 'vue-i18n';
import { en, ru } from 'vuetify/locale';
import {default as msg_en} from './../Translations/en.js';
import {default as msg_ru} from './../Translations/ru.js';

const messages = {
    en: {
        $vuetify: {
            ...en,
            dataIterator: {
                rowsPerPageText: 'Items per page:',
                pageText: '{0}-{1} of {2}',
            },
        },
        ...msg_en,
        custom: {
            ddd: 'DDD'
        }
    },
    ru: {
        $vuetify: {
            ...ru,
            dataIterator: {
                rowsPerPageText: 'Строк на странице:',
                pageText: '{0}-{1} из {2}',
            },
        },
        ...msg_ru,
        custom: {
            ddd: 'ДДД'
        }
    },
}

const supported = ['en', 'es', 'pt', 'de', 'fr', 'ar', 'ko', 'ru', 'zh', 'ja', 'pl'];
const { 0: browserLang } = navigator.language.split('-');


const i18n = createI18n({
    legacy: false, // Vuetify does not support the legacy mode of vue-i18n
    locale: localStorage.getItem('locale') ? localStorage.getItem('locale') : supported.includes(browserLang) ? browserLang : 'en',
    fallbackLocale: 'en',
    messages
});
export default i18n;
export const adapter = createVueI18nAdapter({ i18n, useI18n });
