<script setup>
import {useTheme} from "vuetify";
import logo_dark from "@/Assets/logo_dark.png"
import logo_light from "@/Assets/logo_light.png"

const name = import.meta.env.VITE_PRODUCT_NAME;
const version = import.meta.env.VITE_PRODUCT_VERSION;
const theme = useTheme()
</script>
<template>
    <v-list>
        <v-list-item
            :prepend-avatar="theme.global.current.value.dark ? logo_dark : logo_light"
            :title="name"
            :subtitle="version"
        >
        </v-list-item>
    </v-list>
</template>
