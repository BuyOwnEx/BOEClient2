<script setup>
import { Link } from '@inertiajs/vue3';
import ChangeLocale from "@/Components/ChangeLocale.vue";
import ChangeTheme from "@/Components/ChangeTheme.vue";
import {useDisplay, useTheme} from "vuetify";
import ActionMessage from '@/Components/ActionMessage.vue';
import Banner from '@/Components/Banner.vue';


const { smAndDown } = useDisplay();
const theme = useTheme()
const name = import.meta.env.VITE_PRODUCT_NAME;
const version = import.meta.env.VITE_PRODUCT_VERSION;
//import logo_dark from "@/Assets/logo_auth_dark.png";
//import logo_light from "@/Assets/logo_auth_light.png";

</script>
<template>
    <v-app>
        <div class="auth-layout flex-md-row ">
            <div class="auth-layout__header">
                <ChangeLocale class="auth-layout__lang"></ChangeLocale>
                <ChangeTheme class="auth-layout__theme mr-md-0 ml-md-0" :class="[$vuetify.rtl ? 'mr-1' : 'ml-1']"></ChangeTheme>
            </div>

            <v-sheet class="auth-layout__side mx-auto d-none d-md-flex flex-md-column justify-space-between">
                <div class="auth-layout__side-top mt-3 mt-md-1 pa-2">
                    <slot name="logo" />
<!--                    <a href="/">
                        <v-img min-height="103" max-width="250" :src="theme.global.current.value.dark ? logo_dark : logo_light" class="ma-auto" />
                    </a>-->
                    <div class="auth-layout__slogan title my-2 text-white">
                        {{ $t('product.slogan') }}
                    </div>
                </div>

                <div class="auth-layout__links text-overline pa-1 mb-1">
				<span>
                    <Link :href="route('main')">{{ $t('menu.trading') }}</Link>
                    <Link class="white--text" :href="route('fees')">{{ $t('fees.title') }}</Link>
                    <Link class="white--text" :href="route('status')">{{ $t('status.title') }}</Link>
                    <Link class="white--text" :href="route('api')">{{ $t('menu.api') }}</Link>
				</span>
                </div>
            </v-sheet>

            <div class="pa-2 pa-md-4 pt-9 pt-md-12 flex-grow-1 align-center justify-center d-flex flex-column">
                <div class="layout-content ma-auto w-full">
                    <slot />
                </div>

                <div class="text-overline mt-4">
                    <div v-if="smAndDown" class="auth-layout__links mb-1">
                        <Link :href="route('main')">{{ $t('menu.trading') }}</Link>
                        <Link :href="route('fees')">{{ $t('fees.title') }}</Link>
                        <Link :href="route('status')">{{ $t('status.title') }}</Link>
                        <Link :href="route('api')">{{ $t('menu.api') }}</Link>
                    </div>
                    <div>{{ name }} - {{ version }}</div>
                </div>
            </div>
        </div>
    </v-app>
<!--    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div>
            <slot name="logo" />
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <slot />
        </div>
    </div>-->
</template>
<style scoped lang="scss">
.layout-content {
    max-width: 480px;
}
.w-full {
    width: 100% !important;
}
.waves {
    width: 100%;
    max-height: 100%;
    position: absolute;
    top: 0;
    left: -2px;
    height: 100vh;
}

.auth-layout {
    display: flex;
    flex-flow: column;
    flex-grow: 1;

    &__header {
        position: absolute;
        top: 32px;
        right: 32px;
        z-index: 1;
    }
    &__side {
        width: 420px;
        background: rgba(0, 0, 0, 0) linear-gradient(5deg, rgb(20, 47, 109) 0%, rgb(9, 28, 72) 40%) repeat scroll 0% 0%;
    }
    &__slogan {
        z-index: 1;
        text-align: center;
    }
    &__links {
        z-index: 1;
        text-align: center;
        a {
            text-decoration: none;
            color: unset;
            margin-right: 8px;
        }
    }
}

.v-application--is-rtl .auth-layout {
    &__header {
        text-align: left;
        left: 32px;
        right: unset;
    }
}

.v-theme--dark .auth-layout {
    background-color: #222;
    &__side {
        background: rgba(0, 0, 0, 0) linear-gradient(5deg, rgba(3, 164, 194, 0.55) 0%, rgba(255, 255, 255, 0) 60%) repeat
        scroll 0 0;
    }
}

@media screen and (max-width: 959px) {
    .auth-layout {
        &__header {
            top: 16px;
            right: 8px;
        }
    }
    .v-application--is-rtl .auth-layout {
        &__header {
            left: 8px;
        }
    }
}
</style>
