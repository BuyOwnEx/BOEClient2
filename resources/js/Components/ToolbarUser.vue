<script setup>
import { createAvatar } from '@dicebear/core';
import * as identicon from '@dicebear/identicon';
import * as initials from '@dicebear/initials';
import {
    mdiLogoutVariant,
    mdiTabletDashboard,
    mdiAccountBoxOutline,
    mdiCogOutline

} from '@mdi/js'
import {router} from "@inertiajs/vue3";
import {computed} from "vue";

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
});
const avatar_type = import.meta.env.VITE_USER_ICON_TYPE || 'identicon';
const avatar = computed(() => {
    if(avatar_type === 'initials')
    {
        return createAvatar(initials, {
            seed: props.user.name,
        });
    }
    else
    {
        return createAvatar(identicon, {
            seed: props.user.name,
        });
    }
})
const isVerified = props.user.email_verified_at !== null;
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
    <v-menu offset="14" location="bottom" transition="slide-y-transition">
        <template v-slot:activator="{ props }">
            <v-btn tile variant="plain" v-bind="props">
                <v-badge color="success" offset-x="-3" offset-y="-3" dot>
                    <v-avatar tile v-html="avatar" size="35" />
                </v-badge>
            </v-btn>
        </template>

        <v-list density="compact" nav>
            <v-list-item link @click="profile" v-if="isVerified">
                <template v-slot:prepend>
                    <v-icon small :icon="mdiAccountBoxOutline"></v-icon>
                </template>
                <v-list-item-title>{{ $t('menu.profile') }}</v-list-item-title>
            </v-list-item>
            <v-list-item link @click="settings" v-if="isVerified">
                <template v-slot:prepend>
                    <v-icon small :icon="mdiCogOutline"></v-icon>
                </template>
                <v-list-item-title>{{ $t('menu.settings') }}</v-list-item-title>
            </v-list-item>
            <v-list-item link @click="dashboard" v-if="isVerified">
                <template v-slot:prepend>
                    <v-icon small :icon="mdiTabletDashboard"></v-icon>
                </template>
                <v-list-item-title>{{ $t('menu.dashboard') }}</v-list-item-title>
            </v-list-item>
            <v-divider class="my-1" v-if="isVerified" />

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
