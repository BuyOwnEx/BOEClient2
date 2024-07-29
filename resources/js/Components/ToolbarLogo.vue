<script setup>
import {useTheme} from "vuetify";
import logo_dark from "@/Assets/logo_dark.png"
import logo_light from "@/Assets/logo_light.png"

const name = import.meta.env.VITE_PRODUCT_NAME;
const version = import.meta.env.VITE_PRODUCT_VERSION;
const theme = useTheme()
const lines = 'two';
</script>
<template>
    <v-list-item
        :lines="lines"
        :title="name"
        :subtitle="version"
        :prepend-avatar="theme.global.current.value.dark ? logo_dark : logo_light"
        class="pa-1"
    >
    </v-list-item>
</template>
