<script setup>
import { createAvatar } from '@dicebear/core';
import * as identicon from '@dicebear/identicon';
import {
    mdiLogoutVariant,
    mdiTabletDashboard,
    mdiAccountBoxOutline,
    mdiCogOutline

} from '@mdi/js'
import {router} from "@inertiajs/vue3";

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
});
const avatar = createAvatar(identicon, {
    seed: props.user.name,
    // ... other options
});
const logout = () => {
    router.post(route('logout'));
};
const profile = (item) => {
    router.get(route('profile.show'));
};
const settings = (item) => {
    router.get(route('profile.settings'));
};
const dashboard = (item) => {
    router.get(route('dashboard'));
};

</script>
<template>
    <v-menu offset-y location="left" transition="slide-y-transition">
        <template v-slot:activator="{ props }">
            <v-btn icon class="elevation-2" v-bind="props">
                <v-badge color="success" offset-x="10" offset-y="10" dot bordered>
                    <v-avatar v-html="avatar" size="40" />
                </v-badge>
            </v-btn>
        </template>

        <v-list density="compact" nav>
            <v-list-item link @click="profile">
                <template v-slot:prepend>
                    <v-icon small :icon="mdiAccountBoxOutline"></v-icon>
                </template>
                <v-list-item-title>{{ $t('menu.profile') }}</v-list-item-title>
            </v-list-item>
            <v-list-item link @click="settings">
                <template v-slot:prepend>
                    <v-icon small :icon="mdiCogOutline"></v-icon>
                </template>
                <v-list-item-title>{{ $t('menu.settings') }}</v-list-item-title>
            </v-list-item>
            <v-list-item link @click="dashboard">
                <template v-slot:prepend>
                    <v-icon small :icon="mdiTabletDashboard"></v-icon>
                </template>
                <v-list-item-title>{{ $t('menu.dashboard') }}</v-list-item-title>
            </v-list-item>
            <v-divider class="my-1" />

            <v-list-item link @click="logout">
                <template v-slot:prepend>
                    <v-icon small :icon="mdiLogoutVariant"></v-icon>
                </template>
                <v-list-item-title>{{ $t('menu.logout') }}</v-list-item-title>
            </v-list-item>
        </v-list>
    </v-menu>
</template>

<style scoped>

</style>
