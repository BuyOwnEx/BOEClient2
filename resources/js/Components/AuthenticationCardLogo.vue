<script setup>
    import { Link } from '@inertiajs/vue3';
    import { useTheme } from "vuetify";
    import logo_dark from "@/Assets/logo_auth_dark.png";
    import logo_light from "@/Assets/logo_auth_light.png";

    const theme = useTheme()
</script>

<template>
    <Link :href="route('login')">
    <v-img min-height="103" max-width="250" :src="theme.global.current.value.dark ? logo_dark : logo_light"
        class="ma-auto" />
    </Link>
</template>
