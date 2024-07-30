<script setup>
import { useRtl } from 'vuetify'
import {
    mdiViewGridOutline,
    mdiApps
} from '@mdi/js'

const { isRtl } = useRtl()
const availableApps = [
    {
        title: 'apps.exchange.title',
        subtitle: 'apps.exchange.subtitle',
        icon: mdiApps,
        to: '/',
    }];
</script>

<template>
    <v-menu
        location='bottom'
        :class="[isRtl.value ? 'ml-2' : 'mr-2']"
    >
        <template v-slot:activator="{ props: menu }">
            <v-btn icon v-bind='menu'>
                <v-icon :icon="mdiViewGridOutline"></v-icon>
            </v-btn>
        </template>
        <v-list density="compact" nav>
            <v-list-item
                v-for='app in availableApps' :key='app.title'
                :prepend-icon="app.icon"
                :title="$t(app.title)"
                :subtitle="$t(app.subtitle)"
                :to="app.to"
                density="compact"
            ></v-list-item>
        </v-list>
    </v-menu>
</template>
