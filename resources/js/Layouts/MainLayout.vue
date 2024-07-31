<script setup>
import {computed, ref} from "vue";
import { Head, Link } from '@inertiajs/vue3';
import ChangeTheme from "@/Components/ChangeTheme.vue";
import ToolbarLogo from "@/Components/ToolbarLogo.vue";
import ChangeLocale from "@/Components/ChangeLocale.vue";
import AsideMenu from "@/Components/AsideMenu.vue";
import ToolbarApps from "@/Components/ToolbarApps.vue";
import Banner from '@/Components/Banner.vue';
import ToolbarUser from "@/Components/ToolbarUser.vue";
import { useRtl, useDisplay } from "vuetify";
const drawer = ref(null);
const isContentBoxed = ref(false);
defineProps({
    title: String,
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});
const { isRtl } = useRtl()
const { mobile } = useDisplay()
const isRtlUsed = computed(() => {
    return isRtl.value
})
const copyright = import.meta.env.VITE_CONFIG_COPYRIGHT;
const copy_start_year = import.meta.env.VITE_CONFIG_COPY_START_YEAR;
const all_rights_text = import.meta.env.VITE_CONFIG_ALL_RIGHTS_TEXT;

</script>

<template>
    <v-app>
        <Head :title="title" />
        <Banner />
        <v-navigation-drawer
            v-model="drawer"
            rail
            expand-on-hover
            v-if="$page.props.auth.user && $page.props.auth.user.email_verified_at"
        >
            <ToolbarLogo></ToolbarLogo>
            <v-divider></v-divider>
            <AsideMenu></AsideMenu>
        </v-navigation-drawer>
        <v-app-bar>
            <ToolbarLogo v-if="!$page.props.auth.user || ($page.props.auth.user && !$page.props.auth.user.email_verified_at)"></ToolbarLogo>
            <v-app-bar-nav-icon v-if="$page.props.auth.user && $page.props.auth.user.email_verified_at" @click.stop="drawer = !drawer" />
            <v-spacer class="d-block" />
            <ChangeTheme></ChangeTheme>
            <ChangeLocale></ChangeLocale>
            <ToolbarApps></ToolbarApps>
            <v-btn v-if="!$page.props.auth.user" :to="route('login')" density="comfortable" variant="flat">{{ $t('menu.login') }}</v-btn>
            <v-btn v-if="!$page.props.auth.user" :to="route('register')" base-color="primary" density="comfortable" tile variant="flat" :class="[isRtl.value ? 'ml-1' : 'mr-1']">{{ $t('menu.register') }}</v-btn>
            <ToolbarUser v-if="$page.props.auth.user" :user="$page.props.auth.user"></ToolbarUser>
        </v-app-bar>
        <v-main>
            <v-container class="fill-height" :fluid="!isContentBoxed">
                <v-layout>
                    <slot></slot>
                </v-layout>
            </v-container>
        </v-main>
        <v-footer class="footer overline d-flex" :class="[mobile ? 'flex-column align-start' : '']" tile app>
            <span :style="[mobile ? 'line-height:1' : '']">
                <Link color="primary" class="text-overline text-decoration-none small-font" :class="[isRtlUsed ? 'ml-1' : 'mr-1']" :href="route('status')">{{ $t('status.title') }}</Link>
                <Link color="primary" class="text-overline text-decoration-none small-font" :class="[isRtlUsed ? 'ml-1' : 'mr-1']" :href="route('fees')">{{ $t('fees.title') }}</Link>
                <Link color="primary" class="text-overline text-decoration-none small-font" :class="[isRtlUsed ? 'ml-1' : 'mr-1']" :href="route('contacts')">{{ $t('menu.contacts') }}</Link>
                <Link color="primary" class="text-overline text-decoration-none small-font" :class="[isRtlUsed ? 'ml-1' : 'mr-1']" :href="route('overview')">{{ $t('menu.overview') }}</Link>
                <Link color="primary" class="text-overline text-decoration-none small-font" :class="[isRtlUsed ? 'ml-1' : 'mr-1']" :href="route('api')">{{ $t('menu.api') }}</Link>
                <Link color="primary" class="text-overline text-decoration-none small-font" :class="[isRtlUsed ? 'ml-1' : 'mr-1']" :href="route('terms')">{{ $t('docs.terms.title') }}</Link>
                <Link color="primary" class="text-overline text-decoration-none small-font" :class="[isRtlUsed ? 'ml-1' : 'mr-1']" :href="route('policy')">{{ $t('docs.policy.title') }}</Link>
            </span>
            <v-spacer v-if="!mobile"></v-spacer>
            <span class="text-caption text-left"> {{ copyright }} {{ copy_start_year }} - {{ new Date().getFullYear() }}. {{ all_rights_text }}</span>
        </v-footer>
    </v-app>
</template>
<style lang="sass" scoped>
.v-container
    padding: 4px
.v-layout
    height: 100%
.small-font
    font-size: 0.5rem !important
</style>
