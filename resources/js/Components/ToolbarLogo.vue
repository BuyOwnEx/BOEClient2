<script setup>
import {useTheme,useDisplay} from "vuetify";
import logo_dark from "@/Assets/logo_dark.png"
import logo_light from "@/Assets/logo_light.png"
import {computed} from "vue";

const name = import.meta.env.VITE_PRODUCT_NAME;
const version = import.meta.env.VITE_PRODUCT_VERSION;
const theme = useTheme()
let { width } = useDisplay();
const lines = 'two';
const showTitles = computed(() => {
    return width.value >= 580;
})
</script>
<template>
    <v-list-item
        :lines="lines"
        :title="showTitles?name:''"
        :subtitle="showTitles?version:''"
        :prepend-avatar="theme.global.current.value.dark ? logo_dark : logo_light"
        class="pa-1"
    >
    </v-list-item>
</template>
