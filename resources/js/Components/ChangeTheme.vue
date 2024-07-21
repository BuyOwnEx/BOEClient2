<script setup>
import { useTheme } from 'vuetify'
import { mdiWhiteBalanceSunny, mdiMoonWaningCrescent } from '@mdi/js'
const theme = useTheme()
const toggleTheme = () => {
    theme.global.name.value = theme.global.current.value.dark ? 'light' : 'dark';
    localStorage.setItem('theme', theme.global.name.value);
};
</script>

<template>
    <v-btn variant="text" @click="toggleTheme">
        <v-icon :icon="mdiWhiteBalanceSunny" v-if="theme.global.current.value.dark"></v-icon>
        <v-icon :icon="mdiMoonWaningCrescent" v-else></v-icon>
    </v-btn>
</template>


