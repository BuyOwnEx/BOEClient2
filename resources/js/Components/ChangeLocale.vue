<!-- <script setup>
import { useLocale } from 'vuetify'
import { computed } from 'vue'
import { useDisplay, useRtl } from 'vuetify'
// import { ComponentCodeName } from "vue3-flag-svg/manifest/index.js";
import {useForm} from "@inertiajs/vue3";
import _ from 'lodash';

const { current } = useLocale()
const { smAndDown } = useDisplay()
const { isRtl } = useRtl()

const changeLocale = (locale) => {
    const form = useForm({
        lang: locale,
    });
    form.post(route('set_locale'), {
        onSuccess: () => {
            current.value = locale;
            localStorage.setItem('locale', locale);
        },
    });
};

const availableLocales = [
    {
    code: 'en',
    flag: 'us',
    label: 'English',
    isRtl: false,
}, {
    code: 'es',
    flag: 'es',
    label: 'Español',
    isRtl: false,
}, {
    code: 'pt',
    flag: 'pt',
    label: 'Português',
    isRtl: false,
}, {
    code: 'de',
    flag: 'de',
    label: 'Deutsche',
    isRtl: false,
}, {
    code: 'fr',
    flag: 'fr',
    label: 'Français',
    isRtl: false,
}, {
    code: 'ar',
    flag: 'sa',
    label: 'العربية',
    isRtl: true,
}, {
    code: 'ko',
    flag: 'kr',
    label: '한국어',
    isRtl: false,
}, {
    code: 'ru',
    flag: 'ru',
    label: 'Русский',
    isRtl: false,
}, {
    code: 'zh',
    flag: 'cn',
    label: '中文',
    isRtl: false,
}, {
    code: 'ja',
    flag: 'jp',
    label: '日本語',
    isRtl: false,
}, {
    code: 'pl',
    flag: 'pl',
    label: 'Polskie',
    isRtl: false,
}];
const isIconUse = computed(() => {
    return smAndDown.value
})
const currentLocaleLabel = computed(() => {
    return _.find(availableLocales, item => (item.code === current.value))
})


</script>

<template>
    <v-menu
        location='bottom'
    >
        <template v-slot:activator="{ props: menu }">
            <v-btn variant="text" :icon='isIconUse' v-bind='menu'>
                <component
                    :is="ComponentCodeName[currentLocaleLabel.flag].component"
                    style="width: 15px"
                ></component>
                <span v-show='!isIconUse' :class="[isRtl.value ? 'mr-2' : 'ml-2']">{{ currentLocaleLabel.label }}</span>
            </v-btn>
        </template>
        <v-list dense nav>
            <v-list-item v-for='locale in availableLocales' :key='locale.code' @click='changeLocale(locale.code)'>
                <template v-slot:prepend>
                    <component
                        :is="ComponentCodeName[locale.flag].component"
                        style="width: 15px"
                        :class="[isRtl.value ? 'ml-2' : 'mr-2']"
                    ></component>
                </template>
                <v-list-item-title>{{ locale.label }}</v-list-item-title>
            </v-list-item>
        </v-list>
    </v-menu>
</template>
 -->
<template></template>