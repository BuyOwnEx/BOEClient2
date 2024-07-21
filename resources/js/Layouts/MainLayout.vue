<script setup>
import {ref} from "vue";
import ChangeTheme from "@/Components/ChangeTheme.vue";
import ToolbarLogo from "@/Components/ToolbarLogo.vue";
import ChangeLocale from "@/Components/ChangeLocale.vue";
import AsideMenu from "@/Components/AsideMenu.vue";
import ToolbarApps from "@/Components/ToolbarApps.vue";
import Banner from '@/Components/Banner.vue';
import ToolbarUser from "@/Components/ToolbarUser.vue";
const drawer = ref(null);
const isContentBoxed = ref(false);
defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});

</script>

<template>
    <v-app>
        <Banner />
        <v-navigation-drawer
            v-model="drawer"
            rail
            expand-on-hover
            v-if="$page.props.auth.user"
        >
            <ToolbarLogo></ToolbarLogo>
            <v-divider></v-divider>
            <AsideMenu></AsideMenu>
        </v-navigation-drawer>
        <v-app-bar>
            <ToolbarLogo v-if="!$page.props.auth.user"></ToolbarLogo>
            <v-app-bar-nav-icon v-if="$page.props.auth.user" @click.stop="drawer = !drawer" />
            <v-spacer class="d-block" />
            <ChangeTheme></ChangeTheme>
            <ChangeLocale></ChangeLocale>
            <ToolbarApps></ToolbarApps>
            <v-btn v-if="!$page.props.auth.user" :href="route('login')" tile>{{ $t('menu.login') }}</v-btn>
            <v-btn v-if="!$page.props.auth.user" :href="route('register')" tile dark color="primary">{{ $t('menu.register') }}</v-btn>
            <ToolbarUser v-if="$page.props.auth.user" :user="$page.props.auth.user"></ToolbarUser>
        </v-app-bar>
        <v-divider></v-divider>
        <v-main>
            <v-container class="fill-height" :fluid="!isContentBoxed">
                <v-layout>
                    <slot></slot>
                </v-layout>
            </v-container>
        </v-main>
        <v-footer class="footer overline" inset app>
            <span> Copyright © {{ name }} Ltd. 2017 - {{ new Date().getFullYear() }}. All rights reserved </span>
        </v-footer>
    </v-app>
</template>
<style lang="sass" scoped>
.v-layout
    height: 100%
</style>